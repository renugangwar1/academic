<div class="d-grid">
    <div class="card border border-dark">
        <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Settings') }}
            <div class="position-absolute end-0 top-0 d-flex p-2">
                <button class="btn btn-dark rounded-0 p-2 py-1 rounded-start border-start border border-white"
                    id="changebg" onclick="clickbg()" title="Change Color">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-paint-bucket" viewBox="0 0 16 16">
                        <path
                            d="M6.192 2.78c-.458-.677-.927-1.248-1.35-1.643a3 3 0 0 0-.71-.515c-.217-.104-.56-.205-.882-.02-.367.213-.427.63-.43.896-.003.304.064.664.173 1.044.196.687.556 1.528 1.035 2.402L.752 8.22c-.277.277-.269.656-.218.918.055.283.187.593.36.903.348.627.92 1.361 1.626 2.068.707.707 1.441 1.278 2.068 1.626.31.173.62.305.903.36.262.05.64.059.918-.218l5.615-5.615c.118.257.092.512.05.939-.03.292-.068.665-.073 1.176v.123h.003a1 1 0 0 0 1.993 0H14v-.057a1 1 0 0 0-.004-.117c-.055-1.25-.7-2.738-1.86-3.494a4 4 0 0 0-.211-.434c-.349-.626-.92-1.36-1.627-2.067S8.857 3.052 8.23 2.704c-.31-.172-.62-.304-.903-.36-.262-.05-.64-.058-.918.219zM4.16 1.867c.381.356.844.922 1.311 1.632l-.704.705c-.382-.727-.66-1.402-.813-1.938a3.3 3.3 0 0 1-.131-.673q.137.09.337.274m.394 3.965c.54.852 1.107 1.567 1.607 2.033a.5.5 0 1 0 .682-.732c-.453-.422-1.017-1.136-1.564-2.027l1.088-1.088q.081.181.183.365c.349.627.92 1.361 1.627 2.068.706.707 1.44 1.278 2.068 1.626q.183.103.365.183l-4.861 4.862-.068-.01c-.137-.027-.342-.104-.608-.252-.524-.292-1.186-.8-1.846-1.46s-1.168-1.32-1.46-1.846c-.147-.265-.225-.47-.251-.607l-.01-.068zm2.87-1.935a2.4 2.4 0 0 1-.241-.561c.135.033.324.11.562.241.524.292 1.186.8 1.846 1.46.45.45.83.901 1.118 1.31a3.5 3.5 0 0 0-1.066.091 11 11 0 0 1-.76-.694c-.66-.66-1.167-1.322-1.458-1.847z" />
                    </svg>
                </button>
                <form id="bgchange">
                    @csrf
                    <input type="hidden" id="bgchangecolor" name="bgchangecolor" />
                    <button type="button"
                        class="btn btn-dark rounded-0 p-2 py-1 rounded-end border-start border border-white"
                        onclick="savebg()" title="SAVE COLOR">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-cloud-upload" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383" />
                            <path fill-rule="evenodd"
                                d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body bg-secondary rounded-bottom-1">
            <h5 class="fw-bold">Portal Opening Date for Insitute Upload Marks</h5>
            <hr>
            <div class="">
                <form id="setting">
                    @csrf
                    <div class="d-flex justify-content-between gap-2 mb-3">
                        <div class="flex-1 w-100">
                            <label for="fromdate" class="form-label fw-bold">Opening Date</label>
                            <input type="date" class="form-control" name="poD"
                                value="{{isset($setting->opening_date) ? $setting->opening_date : old('poD') }}"
                                id="fromdate" placeholder="Opening Date" />
                        </div>
                        <div class="flex-1 w-100">
                            <label for="todate" class="form-label fw-bold">Closing Date</label>
                            <input type="date" class="form-control" name="pcD"
                                value="{{isset($setting->closing_date) ? $setting->closing_date : old('pcD') }}"
                                id="todate" placeholder="Closing Date" />
                        </div>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        @if(Auth::user()->role !== 0)
                        <button type="button" onclick="updateSetting()" id="compilebtn"
                            class="w-100 bg-success border border-dark p-2 rounded text-white font-monospace">Update
                            Setting</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>