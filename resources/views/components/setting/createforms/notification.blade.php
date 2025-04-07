<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Add New Notification Form</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form action="{{Route('admin.notification')}}" method="Post">
        @csrf
        @method('Post')
        <div class="d-flex gap-2 mb-2">
            <div class="form-group flex-fill">
                <label for="Ntitle">Title *</label>
                <input type="text" class="form-control" id="Ntitle" name="Ntitle">
            </div>
            <div class="form-group flex-fill">
                <label for="Ntype">Notification For *</label>
                <select class="form-control text-uppercase" id="Ntype" name="Ntype">
                    <option value="">Select option</option>
                    <option value="student">Student</option>
                    <option value="institute">Institute</option>
                </select>
            </div>
        </div>
        <div class="d-flex gap-2 mb-2">
            <div class="form-group flex-fill">
                <label for="Nformdate">From Date *</label>
                <input type="date" class="form-control" id="Nformdate" name="Nformdate">
            </div>
            <div class="form-group flex-fill">
                <label for="Ntodate">To Date *</label>
                <input type="date" class="form-control" id="Ntodate" name="Ntodate">
            </div>
        </div>
        <div class="d-flex gap-2 mb-2">
            <div class="form-group flex-fill">
                <label for="Nlink">Link *</label>
                <input type="text" class="form-control" id="Nlink" name="Nlink" autocomplete=false>
            </div>
        </div>
        <div class="d-flex gap-2">
            <div class="form-group flex-fill">
                <button class="btn btn-success w-100">Submit</button>
            </div>
        </div>
    </form>
</div>