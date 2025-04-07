<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Institute;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;




use iio\libmergepdf\Merger;

class EnrollmentController extends Controller
{
    protected $table = 'enrollments';

    public function create()
    {

        $institute = auth('institute')->user();


        if (!$institute) {
            return redirect()->route('institute.login')->with('error', 'You need to be logged in as an institute.');
        }

        $type = session('institute_type');

        if ($type == 'master') {
            // Master Chapter sees all enrollments
            $enrollments = Enrollment::all();
        } else {
            // Regular Institute sees only its own enrollments
            $enrollments = Enrollment::where('chapter_name', $institute->InstituteName)->get();
        }

        return view('institute.newenrollment', compact('institute', 'enrollments'));
    }


    /**
     * Preview Enrollment Data
     */
    public function previewEnrollment(Request $request)
    {
        // dd($request->all()); 

        // Validate incoming request data
        $validatedData = $request->validate([
            'institute_name' => 'required|string',
            'year_of_admission' => 'numeric|digits:4',
            'chapter_name' => 'required|string',
            'programme_of_study' => 'required|string',
            'student_name_en' => 'required|string|regex:/^[A-Z ]+$/',
            'student_name_hi' => 'required|regex:/^[\x{0900}-\x{097F} ]+$/u',
            'date_of_birth' => 'required|string',
            'category' => 'required|string',
            'pwbd_category' => 'nullable|string',
            'father_name' => 'required|string|max:255',
            'father_mobile' => 'required|string|digits:10',
            'mother_name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'local_address' => ['required', 'string', 'max:500', 'regex:/\b\d{6}\b/'],
            'permanent_address' => ['required', 'string', 'max:500', 'regex:/\b\d{6}\b/'],
            'state_of_domicile' => 'required|string',
            'nationality' => 'required|string',
            'student_email' => 'required|email|unique:users,email',
            'student_mobile' => 'required|digits:10',
            'abc_id' => 'nullable',
            'regex:/^\d{12}$/',
            'student_image' => '|image|mimes:jpeg,png,jpg,avif,webp|max:100',
            'nchm_roll_no' => 'required|numeric|unique:enrollments,nchm_roll_no',


            // Academic records
            'board_10th' => 'required|string|max:255',
            'school_10th' => 'required|string|max:255',
            'year_10th' => 'required|digits:4',
            'subject_10th' => 'required|string|max:100',
'percentage_10th' => ['required', 'regex:/^(100|\d{1,2}(\.\d{1,2})?|A\+{0,2}|B\+{0,2})$/'],       
     'board_12th' => 'required|string|max:255',
            'school_12th' => 'required|string|max:255',
            'year_12th' => 'required|digits:4',
            'subject_12th' => 'required|string|max:100',
'percentage_12th' => ['required', 'regex:/^(100|\d{1,2}(\.\d{1,2})?|A\+{0,2}|B\+{0,2})$/'],      
      'board_other' => 'nullable|string|max:255',
            'school_other' => 'nullable|string|max:255',
            'year_other' => 'nullable|string|digits:4',
            'subject_other' => 'nullable|string|max:100',
'percentage_other' => ['nullable', 'regex:/^(100|\d{1,2}(\.\d{1,2})?|A\+{0,2}|B\+{0,2})$/'],  
      ]);


        $textFields = ['board_other', 'school_other', 'year_other', 'subject_other'];

        $numericFields = ['percentage_other'];

        foreach ($textFields as $field) {
            if (empty($validatedData[$field])) {
                $validatedData[$field] = 'N/A';
            }
        }

        // Set "00" for empty numeric fields
        foreach ($numericFields as $field) {
            if (empty($validatedData[$field])) {
                $validatedData[$field] = 0; // Store 00 in the database
            }
        }


        $validatedData['pwbd_category'] = $request->input('pwbd_category', 'no');
        $validatedData['hh_oh_vh'] = $request->input('hh_oh_vh', 'N/A');
        $validatedData['percentage'] = $request->input('percentage', 'N/A');
        $validatedData['abc_id'] = $request->input('abc_id', 'N/A');
        // Handle image upload
        if ($request->hasFile('student_image')) {
            $imageName = time() . '.' . $request->student_image->extension();
            $request->student_image->move(public_path('uploads/main'), $imageName);
            $validatedData['student_image'] = 'uploads/main/' . $imageName;
        } else {
            $validatedData['student_image'] = null;
        }

        return view('institute.previewEnrollment', ['data' => $validatedData]);
    }


    public function messages()
    {
        return [
            'student_name_en.regex' => 'The student name in English should only contain uppercase letters and spaces.',
            'student_name_hi.regex' => 'The student name in Hindi should only contain Hindi characters.',
            'father_mobile.digits' => 'The father’s mobile number must be exactly 10 digits.',
            'student_email.email' => 'Please enter a valid email address.',
            'percentage_10th.between' => 'Percentage must be between 0 and 100.',
            'percentage_12th.between' => 'Percentage must be between 0 and 100.',
            'nchm_roll_no.unique' => 'This student record is already registered.',
            'local_address.regex' => 'The local address must include a valid 6-digit PIN code.',
            'permanent_address.regex' => 'The permanent address must include a valid 6-digit PIN code.',
        ];
    }





    /**
     * Store Enrollment Data
     */
    public function store(Request $request)
    {
        // dd($request->all());

        if (!$request->has('data')) {
            return redirect()->back()->with('error', 'No data received.');
        }


        $data = json_decode($request->input('data'), true);

        if (empty($data['percentage']) || !is_numeric($data['percentage'])) {
            $data['percentage'] = 0.00;  // ✅ Store 0 instead of 'N/A'
        }
        if (empty($data['abc_id']) || !is_numeric($data['abc_id'])) {
            $data['abc_id'] = 0.00;  // ✅ Store 0 instead of 'N/A'
        }
        $institute = Institute::where('InstituteName', $data['chapter_name'])->first();

        // Store the fetched `institute_id`
        $data['institute_id'] = $institute->id;

        //   dd($data);

        if (empty($data['chapter_name'])) {
            $data['chapter_name'] = $institute->instituteName;
        }



        $requestdata = collect($data);


        $newcreate = Enrollment::create($requestdata->all());


        return redirect()->route('institute.newenrollment')->with('success', 'Enrollment submitted successfully.');
    }



    public function update(Request $request, $id)
    {

        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json(['error' => 'Enrollment not found.'], 404);
        }

        $validatedData = $request->validate([
            'id' => 'required|exists:enrollments,id',
            'student_name_en' => 'required|string|regex:/^[A-Z\s]+$/',  // Only capital letters
            'student_name_hi' => 'required|string|regex:/^[\x{0900}-\x{097F}\s]+$/u',  // Only Hindi characters
            'student_mobile' => 'required|string|regex:/^\d{10}$/',  // 10 digits
            'father_mobile' => 'nullable|string|regex:/^\d{10}$/',  // 10 digits or null
            'guardian_name' => 'required|string',
            'student_email' => 'nullable|email',  // email format validation
            'local_address' => 'required|string|regex:/\d{6}/',  // 6-digit PIN in address
            'permanent_address' => 'required|string|regex:/\d{6}/',  // 6-digit PIN in address
           'abc_id' => 'nullable|string'
  // 12 digits or N/A
        ]);
    
        // Update the enrollment with validated data
        $enrollment->update($validatedData);
    
        return redirect()->route('institute.newenrollment')->with('success', 'Enrollment updated successfully.');
    }


    public function destroy(Request $request)
    {
        $enrollment = Enrollment::find($request->id);

        if ($enrollment) {
            $enrollment->delete();
            return redirect()->back()->with('success', 'Enrollment deleted successfully!');
        }

        return redirect()->back()->with('error', 'Enrollment not found.');
    }






    public function previewFEnrollment($id)
    {
        // Fetch enrollment details from the database
        $enrollment = Enrollment::findOrFail($id);

        // Return the Blade template from the 'pdf' folder
        return view('pdf.htmlenrollment', compact('enrollment'));
    }








    public function toggleAccess(Request $request)
    {
        // Only the Master Institute can toggle
        if (auth('institute')->user()->type !== 'master') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Toggle the global state (store in cache)
        $isActive = !cache()->get('master_toggle', false); // Default is now false (inactive)
        cache()->put('master_toggle', $isActive);

        return response()->json(['success' => true, 'is_active' => $isActive]);
    }

    // Method to get the current toggle state for all institutes
    public function getToggleState()
    {
        return response()->json(['is_active' => cache()->get('master_toggle', false)]); // Default false
    }




    public function toggleEnrollment(Request $request)
    {
        try {
            $enrollmentId = $request->input('enrollment_id');
            if (!$enrollmentId) {
                return response()->json(['error' => 'Enrollment ID is required'], 400);
            }

            $cacheKey = 'reopen_toggle_' . $enrollmentId;

            // Retrieve current state from cache, default to false if not found
            $currentState = cache()->get($cacheKey, false);
            $newState = !$currentState; // Toggle the state

            // Store the new state in cache for 30 days
            cache()->put($cacheKey, $newState, now()->addDays(30));

            return response()->json([
                'success' => true,
                'enrollment_id' => $enrollmentId,
                'reopen_toggle' => $newState,
                'message' => $newState ? 'Reopen Enabled' : 'Reopen Disabled'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Fetch all toggle states on page load
    public function EnrollmentToggleState()
    {
        try {
            $enrollmentToggles = [];
            $enrollmentIds = Enrollment::pluck('id'); // Get all enrollment IDs

            foreach ($enrollmentIds as $id) {
                $enrollmentToggles[$id] = cache()->get('reopen_toggle_' . $id, false);
            }
            return response()->json($enrollmentToggles);
        } catch (\Exception $e) {
            \Log::error("Fetch Toggle States Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Check logs.'], 500);
        }
    }




    /**
     * Fetch enrollment data for DataTables.
     */
    public function ajaxEnrollmentData(Request $request)
    {

        // return $instituteName;
        try {


            // Log::info('Fetching enrollments for institute:', ['institute_id' => $instituteId]);

            // Fetch enrollments for the institute using the institute_id
            $enrollments = Enrollment::where('institute_id', $instituteId)
                ->with('institute:id,institute_name')  // Load institute with name
                ->get([
                    'id',
                    'year_of_admission',
                    'institute_name',
                    'chapter_name',
                    'programme_of_study',
                    'student_name_en',
                    'student_name_hi',
                    'date_of_birth',
                    'category',
                    'pwbd_category',
                    'hh_oh_vh',
                    'percentage',
                    'father_name',
                    'father_mobile',
                    'mother_name',
                    'guardian_name',
                    'local_address',
                    'permanent_address',
                    'state_of_domicile',
                    'nationality',
                    'student_email',
                    'student_mobile',
                    'abc_id',
                    'student_image',
                    'nchm_roll_no',


                    // Add academic records fields
                    'board_10th',
                    'school_10th',
                    'year_10th',
                    'subject_10th',
                    'percentage_10th',
                    'board_12th',
                    'school_12th',
                    'year_12th',
                    'subject_12th',
                    'percentage_12th',
                    'board_other',
                    'school_other',
                    'year_other',
                    'subject_other',
                    'percentage_other',

                ]);




            return response()->json([
                "data" => $enrollments
            ]);



        } catch (\Exception $e) {
            Log::error('Error fetching enrollments:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}