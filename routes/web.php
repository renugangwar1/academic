<?php

use App\Http\Controllers\Auth\InstituteAuthController;
use App\Http\Controllers\Auth\InstituteForgotPasswordController;
use App\Http\Controllers\Auth\InstituteLoginController;
use App\Http\Controllers\Auth\InstituteResetPasswordController;
use App\Http\Controllers\Auth\InstituteVerificationController;
use App\Http\Controllers\Auth\StudentForgotPasswordController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\TBAdmitCardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\StudentResetPasswordController;
use App\Http\Controllers\Auth\StudentVerificationController;
use App\Http\Controllers\InstituteController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EnrollmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Overwrite the default login route
Route::get('authority/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('authority/login', [LoginController::class, 'login']);

Route::get('/download-errors', [TBAdmitCardController::class,'downloadErrors'])->name('download.errors');


// Route::get('/testedmitcard',[TBAdmitCardController::class,'testedmitcard']);


Auth::routes(['verify' => true]);


//student Routes ///////////////////////////////////////////////////////////////////////////////////////////////
Route::prefix('student')->name('student.')->controller(StudentLoginController::class)->group(function() {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/logout', 'logout')->name('logout');

    Route::get('/email/verify', [StudentVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [StudentVerificationController::class, 'verify'])
        ->middleware(['auth:student', 'signed'])
        ->name('verification.verify');

    Route::post('/email/resend', [StudentVerificationController::class, 'resend'])
        ->middleware(['auth:student', 'throttle:6,1'])
        ->name('verification.resend');

    Route::get('/password/reset', [StudentForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [StudentForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/password/reset', [StudentResetPasswordController::class, 'reset'])->name('password.update');
    Route::get('/student/password/reset/{token}', [StudentResetPasswordController::class, 'showResetForm'])->name('password.reset');
    
    Route::middleware(['auth.student'])->controller(StudentController::class)->group(function() {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/reappearform','reappearform')->name('reappearform');
        Route::post('/reappearform','searchreappear')->name('searchreappear');
        Route::post('/feepayment','feepayment')->name('feepayment');
        Route::get('/report-card/{course}/{sem}/{rno}','search')->name('reportcard');
    });
});




// institute routes///////////////////////////////////////////////////////////////////////////////////////////////////
Route::prefix('institute')->name('institute.')->controller(InstituteLoginController::class)->group(function(){
    Route::get('/login','showloginForm')->name('login');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/logout', 'logout')->name('logout');

    Route::middleware(['auth.institute'])->controller(InstituteVerificationController::class)->group(function() {
        Route::get('/email/verify','notice')->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}','verify')->middleware(['auth:institute', 'signed'])->name('verification.verify');
        Route::post('/email/resend','resend')->middleware(['auth:institute', 'throttle:6,1'])->name('verification.resend');
    });

    Route::get('/password/reset', [InstituteForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [InstituteForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [InstituteResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [InstituteResetPasswordController::class, 'reset'])->name('password.update');

    Route::middleware(['auth.institute'])->controller(InstituteController::class)->group(function() {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/Import',[App\Http\Controllers\ExcelController::class,'Import'])->name('excel.Import');
        Route::post('/importdata',[App\Http\Controllers\ExcelController::class,'importdata'])->name('excel.importdata');
    });

    Route::get('/compile_marks', [App\Http\Controllers\HomeController::class, 'compileResult'])->name('compile_marks');
    
    Route::get('/compiled_print', [App\Http\Controllers\HomeController::class, 'compiledPrint'])->name('compiled_print');
    
    Route::get('/students_list',[Controller::class,'students'])->name('students');
    Route::get('/upload_student_list',[Controller::class,'uploadstudentlist'])->name('uploadstudentlist');
    Route::get('/excel_logs',[Controller::class,'excelLog'])->name('excelLog');
    
    Route::get('/studenttemplate',[ExcelController::class,'studenttemplate'])->name('studentimporttem');
    Route::post('/importstudentlist',[ExcelController::class,'importstudentlist'])->name('importstudentlist');
    Route::get('/studentoptional_template',[ExcelController::class,'tempoptionalstudentlist'])->name('tempoptionalstudentlist');
    Route::post('/updatestudentlist',[ExcelController::class,'updatestudentlist'])->name('updatestudentlist');


      /////////////Enrollement///////
      Route::get('/enrollments', [EnrollmentController::class, 'getEnrollments']);

      Route::get('/institute/newenrollment', [EnrollmentController::class, 'create'])->name('newenrollment');
      // Define the route for storing new enrollment
      Route::post('/institute/newenrollment/store', [EnrollmentController::class, 'store'])->name('newenrollment.store');
  
  
      Route::post('/institute/ajaxenrollmentdata', [EnrollmentController::class, 'ajaxEnrollmentData'])->name('ajaxEnrollmentData');
  
  
      Route::post('institute/previewEnrollment', [EnrollmentController::class, 'previewEnrollment'])->name('previewEnrollment');
  
  
      Route::post('/institute/previewEnrollment/store', [EnrollmentController::class, 'store'])->name('previewEnrollment.store');
      Route::put('/newenrollment/update/{id}', [EnrollmentController::class, 'update'])->name('newenrollment.update');
      Route::post('/institute/deleteEnrollment', [EnrollmentController::class, 'destroy'])->name('deleteEnrollment');
  
  
      Route::get('/institute/enrollment/download/{id}', [EnrollmentController::class, 'downloadPDF'])
          ->name('enrollment.download');
      //  Route::post('/store-merged-pdf', [EnrollmentController::class, 'storeMergedPdf'])->name('store.merged.pdf');
  
  
      Route::post('/enrollments/toggle', [EnrollmentController::class, 'toggleAccess'])
          ->name('enrollments.toggle');
  
      Route::get('/enrollments/toggle-state', [EnrollmentController::class, 'getToggleState']);
  
  
      Route::post('/enrollment/toggleEnrollment', [EnrollmentController::class, 'toggleEnrollment'])->name('enrollment.toggleEnrollment');
  
      Route::get('/enrollment-toggle-state', [EnrollmentController::class, 'EnrollmentToggleState'])
  
          ->name('EnrollmentToggleState');
  
  
  
      Route::get('/institute/enrollment/previewF/{id}', [EnrollmentController::class, 'previewFEnrollment'])->name('enrollment.previewF');
});


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('welcome');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/studentlogin',function(){return view('auth.studentlogin');})->name('studentlogin');

// Route::get('/admitcard',function(){
//     return view('admitcard');
// });




Route::middleware('auth')->group(function(){
    
    Route::Get('/search',[TBAdmitCardController::class,'search'])->name('search');
    
    Route::post('/bgcolorchange',[App\Http\Controllers\Controller::class, 'bgcolorchange'])->name('bgcolorchange');
    
    /////////////////////////////// compile Routes ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    Route::get('/compile_marks', [App\Http\Controllers\HomeController::class, 'compileResult'])->name('compile_marks');
    
    Route::post('/compiling_result', [App\Http\Controllers\HomeController::class, 'compilingResult'])->name('compiling_result');
    
    Route::post('/compiled_view', [App\Http\Controllers\HomeController::class, 'compiledview'])->name('compiled_view');
    
    // Route::post('/compiled_print', [App\Http\Controllers\HomeController::class, 'compiledPrint'])->name('compiled_print');
    
    Route::get('/compiled_print', [App\Http\Controllers\HomeController::class, 'compiledPrint'])->name('compiled_print');
    
    ////////////////////////////// end here //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //////////////////////////////// generate result routes /////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    Route::get('/generate_result', [App\Http\Controllers\HomeController::class, 'generateResult'])->name('generate_result');
    
    Route::post('/generating_result', [App\Http\Controllers\HomeController::class, 'generatingResult'])->name('generating_result');

    Route::post('/generating_cgpa', [App\Http\Controllers\HomeController::class, 'generatingCGPA'])->name('generating_cgpa');
    
    Route::post('/view_generating_result', [App\Http\Controllers\HomeController::class, 'viewgeneratingResult'])->name('view_generating_result');
    
    Route::get('/generated_print', [App\Http\Controllers\HomeController::class, 'generatedPrint'])->name('generated_print');
    
    
    Route::get('/jnuresult',[App\Http\Controllers\HomeController::class, 'jnuresult'])->name('jnuresult');
    
    Route::get('/postjnuresult',[App\Http\Controllers\HomeController::class, 'showjnuresult'])->name('showjnuresult');
});


Route::middleware(['strictauth','preventRepeatedRequests'])->controller(ExcelController::class)->group(function () {
    Route::get('/Import','Import')->name('excel.Import');
    Route::get('/export','export')->name('excel.export');
    Route::post('/exportdata','exportdata')->name('excel.exportdata');
    Route::post('/importdata','importdata')->name('excel.importdata');
    Route::post('/template','template')->name('excel.template');
    Route::post('/exportjnu','exportjnu')->name('excel.exportjnuresult');
    
    Route::get('/viewdata','exportshow')->name('excel.viewdata');
    Route::post('/exportshow','viewexcel')->name('excel.viewexcel');
    Route::get('/view_history','viewhistory')->name('excel.viewhistory');
    Route::post('/searchexcel','searchexcel')->name('excel.searchexcel');
    Route::get('/instituteview','instituteview')->name('excel.instituteview');
    Route::post('/course_wise_dataview','coursewiseview')->name('excel.searchinstituteview');
    Route::post('/subject_wise_dataview','subjectwiseview')->name('excel.subjectwiseview');
    Route::get('/findsubject',[TBAdmitCardController::class,'findsubject'])->name('excel.findsubject');
    Route::post('/failsubjects','failsubjects')->name('excel.failsubjects');
    Route::get('/printerData','printerData')->name('excel.printerData');
    Route::post('/printerData','getprinterData')->name('excel.getprinterData');
    Route::post('/exportprinterData','exportprinterData')->name('excel.exportprinterData');
});

Route::middleware(['auth','role:3'])->prefix('admin')->name('admin.')->controller(Controller::class)->group(function () {
    Route::get('/coursemaster','coursemaster')->name('coursemaster');
    Route::post('/addcours','addcours')->name('addcours');
    Route::post('/deletecourse','deletecourse')->name('deletecourse');

    Route::get('/subjectmaster','subjectmaster')->name('subjectmaster');
    Route::post('/addsubject','addsubject')->name('addsubject');
    Route::post('/updatesubject','updatesubject')->name('updatesubject');
    Route::post('/deletesubject','deletesubject')->name('deletesubject');
    Route::get('/institutemaster','institutemaster')->name('institutemaster');
    Route::post('/updateorcreateinstitute','updateorcreateinstitute')->name('updateorcreateinstitute');
    Route::post('/deleteinstitute','deleteinstitute')->name('deleteinstitute');
    Route::get('/academicdata','academicdata')->name('academicdata');
    Route::get('/users','users')->name('users');
    Route::post('/updateorcreateuser','updateorcreateuser')->name('updateorcreateuser');
    Route::post('/userdelete','userdelete')->name('userdelete');
    Route::get('/excel_logs','excelLog')->name('excelLog');
    Route::get('/setting','settings')->name('setting');
    Route::post('/setting','updatesettings')->name('settingupdate');
    Route::post('/Reappearsetting','Reappearsetting')->name('Reappearsetting');
    Route::post('/deleteReappearsetting','deleteReappearsetting')->name('deleteReappearsetting');
    Route::post('/notification','notification')->name('notification');
    Route::post('/deletenotification','deletenotification')->name('deletenotification');
    Route::get('/students_list','students')->name('students');
    Route::get('/students_list/{batch}','batchstudentlist')->name('batchstudentlist');
    Route::get('/upload_student_list','uploadstudentlist')->name('uploadstudentlist');
    Route::get('/studenttemplate',[ExcelController::class,'studenttemplate'])->name('studentimporttem');
    Route::post('/importstudentlist',[ExcelController::class,'importstudentlist'])->name('importstudentlist');
    Route::post('/deletestudent','deletestudent')->name('deletestudent');
    Route::get('/studentoptional_template',[ExcelController::class,'tempoptionalstudentlist'])->name('tempoptionalstudentlist');
    Route::post('/updatestudentlist',[ExcelController::class,'updatestudentlist'])->name('updatestudentlist');

    Route::get('/it_student_list','itstudentList')->name('itstudentList');
    Route::post('/search_it_student','searchforitstudent')->name('searchforitstudent');
    Route::post('/transfer_to_IT','transfertoitstudents')->name('transfertoitstudents');
    Route::post('/admitcard_for_IT','itadmitcardprint')->name('itadmitcardprint');
    Route::post('/download_IT_admitcard','printitadmicard')->name('printitadmicard');
});