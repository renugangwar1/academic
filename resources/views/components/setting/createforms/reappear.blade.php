<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Add New Reappear Form</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form action="{{Route('admin.Reappearsetting')}}" method="Post">
        @csrf
        @method('Post')
        <div class="d-flex gap-2 mb-2">
            <div class="form-group flex-fill">
                <label for="course">Select Course</label>
                <select class="form-control text-uppercase" id="course" name="course">
                    <option value="">Select Course</option>
                    @foreach($course as $duration=>$single)
                    <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group flex-fill">
                <label for="batch">Select Batch *</label>
                <select class="form-control text-uppercase" id="batch" name="batch">
                    <option value="">Select Batch</option>
                    @foreach(batch() as $batch)
                    <option value="{{$batch}}">{{$batch}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group flex-fill">
                <label for="semester">Select Semester *</label>
                <select class="form-control text-uppercase" id="semester" name="semester">
                    <option value="">Select Option</option>
                    @foreach($semester as $vlaue)
                    <option value="{{$vlaue}}">{{$vlaue}} Semester</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="d-flex gap-2 mb-2">
            <div class="form-group flex-fill">
                <label for="Reappear_from_date">From Date *</label>
                <input type="date" class="form-control" id="Reappear_from_date" name="Reappear_from_date">
            </div>
            <div class="form-group flex-fill">
                <label for="Reappear_to_date">To Date *</label>
                <input type="date" class="form-control" id="Reappear_to_date" name="Reappear_to_date">
            </div>
        </div>
        <div class="d-flex gap-2 mb-2">
            <div class="form-group flex-fill">
                <label for="Reappear_late_fee_date">Late Fee Date *</label>
                <input type="date" class="form-control" id="Reappear_late_fee_date" name="Reappear_late_fee_date">
            </div>
            <div class="form-group flex-fill">
                <label for="Reappear_late_fee">Late Fee Amount *</label>
                <input type="text" class="form-control" id="Reappear_late_fee" name="Reappear_late_fee">
            </div>
        </div>
        <div class="d-flex gap-2">
            <div class="form-group flex-fill">
                <button class="btn btn-success w-100">Submit</button>
            </div>
        </div>
    </form>
</div>