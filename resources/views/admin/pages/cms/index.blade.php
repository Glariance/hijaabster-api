@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Manage CMS')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">CMS</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item active" aria-current="page">{{ $page->page_title }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group  ">
                        <button type="button" class="btn btn-light px-5">Page Settings</button>
                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"><span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                            <a class="dropdown-item" href="javascript:;"
                                onclick="showAjaxModal('Edit Page Detail', 'Update', `{{ route('admin.cms.page.create', ['slug' => $slug]) }}`)">Edit</a>
                            <a class="dropdown-item" href="javascript:;"
                                onclick="deletePage({{ $page->id }}, `{{ route('admin.cms.page.delete', ['id' => $page->id]) }}`)">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="col-lg-3 border-end pe-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Sections</h5>
                                <button type="button" class="btn btn-light"
                                    onclick="showAjaxModal(`Create New Section in {{ $page->page_title }} Page`, 'Create', `{{ route('admin.cms.section.create', ['pageId' => $page->id]) }}`)">
                                    <i class="bx bx-plus mr-1"></i>
                                    Add
                                </button>
                            </div>
                            <hr>
                            <ul class="nav flex-column border-0 mt-5">
                                @foreach ($page->sections()->orderBy('section_sort_order')->get() as $section)
                                    <div class="btn-group btn-group-2  mb-4" id="page-section-{{ $section->id }}">
                                        <button type="button"
                                            onclick="loadPage(`{{ route('admin.cms.section.fields', ['sectionId' => $section->id]) }}`, '#load-field-page')"
                                            class="btn-1 btn btn-light">{{ $section->section_name }}</button>
                                        <i class="lni lni-more-alt btn-2  dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false"> <span
                                                class="visually-hidden">Toggle Dropdown</span></i>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"
                                            style="margin: 0px;">
                                            <a class="dropdown-item" href="javascript:;"
                                                onclick="loadPage(`{{ route('admin.cms.section.fields', ['sectionId' => $section->id]) }}`, '#load-field-page')">View</a>
                                            <a class="dropdown-item" href="javascript:;"
                                                onclick="showAjaxModal('Edit Section Detail in {{ $page->page_title }} Page', 'Update', `{{ route('admin.cms.section.create', ['id' => $section->id]) }}`)">Edit</a>
                                            <a class="dropdown-item" href="javascript:;"
                                                onclick="deleteSection(`{{ $section->id }}`, `{{ route('admin.cms.section.delete', ['id' => $section->id]) }}`)">Delete</a>
                                        </div>
                                    </div>
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-lg-9">
                            <div class="card-body shadow-sm" id="load-field-page">
                                {{-- Load Field Page base on selected Section --}}

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(function() {
                // delete Pages
                window.deletePage = function(settingId, url) {
                    confirmDelete(() => {
                        handleAjaxRequest(url, function() {
                            window.location.href = "{{ route('admin.dashboard') }}";
                        });
                    });
                }

                // dekete Sections 
                window.deleteSection = function(settingId, url) {
                    confirmDelete(() => {
                        handleAjaxRequest(url, function() {
                            $('#page-section-' + settingId).remove();
                            $('#load-field-page').html('');
                        });
                    });
                }
                // Create Or Update Section details
                ajaxPost("#cms-section-form", "#cms-section-btn", function(response) {
                    successMessage(response.success);
                    $('#custom-lg-modal').modal('hide');
                    window.location.reload();
                }, function(xhr) {
                    // error code here
                });

                // update or Create section fields
                ajaxPost("#cms-section-field-form", "#btnSectionField", function(response) {
                    let sectionId = response.section_id; // Get section ID from response
                    let url = `{{ route('admin.cms.section.fields', ['sectionId' => '__SECTION_ID__']) }}`
                        .replace('__SECTION_ID__', sectionId);

                    loadPage(url, '#load-field-page'); // Load the page dynamically
                    successMessage(response.success);
                }, function(xhr) {
                    // Handle error here
                });

                // for delete section fields
                window.deleteField = function(settingId, url) {
                    confirmDelete(async () => {
                        handleAjaxRequest(url, function(response) {
                            let sectionId = response.section_id; // Get section ID from response
                            let urlForLoad =
                                `{{ route('admin.cms.section.fields', ['sectionId' => '__SECTION_ID__']) }}`
                                .replace('__SECTION_ID__', sectionId);
                            loadPage(urlForLoad,
                                '#load-field-page'); // Load the page dynamically
                        });
                    });
                }
                // Add Fields in Group
                ajaxPost("#cms-section-group-field-form", "#cms-section-group-field-btn", function(response) {
                    let sectionId = response.section_id; // Get section ID from response
                    let url = `{{ route('admin.cms.section.fields', ['sectionId' => '__SECTION_ID__']) }}`
                        .replace('__SECTION_ID__', sectionId);

                    loadPage(url, '#load-field-page'); // Load the page dynamically
                    $('#custom-lg-modal').modal('hide');
                    successMessage(response.success);
                }, function(xhr) {
                    // Handle error here
                });

            })
        </script>
    @endpush
@endsection
