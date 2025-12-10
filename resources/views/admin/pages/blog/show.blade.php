<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($blog->media as $media)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                @if ($media->media_type == 'image')
                                    <img src="{{ asset($media->path) }}" class="d-block w-100" alt="...">
                                @else
                                    <video src="{{ asset($media->path) }}" controls class="d-block w-100"></video>
                                @endif
                            </div>
                        @endforeach
                        {{-- <div class="carousel-item active">
                            <img src="{{ asset('adminassets/images/gallery/20.png') }}" class="d-block w-100"
                                alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('adminassets/images/gallery/21.png') }}" class="d-block w-100"
                                alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('adminassets/images/gallery/22.png') }}" class="d-block w-100"
                                alt="...">
                        </div> --}}
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                        data-bs-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                        data-bs-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">{{ $blog->title }}</h6>
                <label class="form-label mt-5 d-block">Tags: </label>
                @forelse ($blog->tags as $tag)
                    {!! defaultBadge($tag->name, 25) !!}
                @empty
                    {!! defaultBadge('Not Selected') !!}
                @endforelse
                <div class="d-flex mt-5">
                    <div class="col-6">
                        <label class="form-label mt-2 d-block">Created Date: </label>
                        {!! defaultBadge($blog->created_at->format('d-M-Y')) !!}
                    </div>
                    <div class="col-6">
                        <label class="form-label mt-2 d-block">Status: </label>
                        {!! defaultBadge(strtoupper($blog->status)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h6 class="mb-0 text-uppercase">Meta Details:</h6>
        <hr>
        <div class="card radius-10">
            <label class="form-label d-block">Title: </label>
            {!! $blog->meta_title !!}
            <label class="form-label mt-2 d-block">Keywords: </label>
            {!! $blog->meta_keyword !!}
            <label class="form-label mt-2 d-block">Description: </label>
            {!! $blog->meta_description !!}
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h6 class="mb-0 text-uppercase">Blog Description:</h6>
        <hr>
        <div class="card radius-10">
            {!! $blog->description !!}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-0 text-uppercase">Comments</h6>
        <hr>
        <div class="card radius-10 overflow-auto" style="height: 500px;">
            <div class="card-body">
                @forelse ($blog->comments as $comment)
                    <div class="d-flex mb-4" id="comment-row-id-{{ $comment->id }}">
                        <img src="{{ $comment->user->image ?? asset('adminassets/images/avatars/avatar-2.png') }}"
                            class="rounded-circle p-1 border" width="90" height="90" alt="...">
                        <div class="flex-grow-1 ms-3">
                            <div class="row justify-content-between setting-input-group">
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="mt-0">{{ $comment->user->name ?? 'Anonymous' }}</h5>
                                        {!! defaultBadge(strtoupper($comment->status), 'auto') !!}
                                    </div>
                                </div>
                                <div class="col-lg-4 text-end d-flex">
                                    @if ($comment->status == 'pending')
                                        <div id="comment-status-btn-id-{{$comment->id}}">
                                            <button class="btn btn-light btn-sm taxt-end delete-btn" title="Approve"
                                                onclick="updateStatus({{ $comment->id }}, '{{ route('admin.blogs.updateCommentStatus', [$comment->id, 'status' => 'approved']) }}')"><i
                                                    class="lni lni-checkmark-circle"></i></button>
                                            <button class="btn btn-light btn-sm taxt-end delete-btn" title="Reject"
                                                onclick="updateStatus({{ $comment->id }}, '{{ route('admin.blogs.updateCommentStatus', [$comment->id, 'status' => 'rejected']) }}')"><i
                                                    class="lni lni-cross-circle"></i></button>
                                        </div>
                                    @endif
                                    <button class="btn btn-light btn-sm taxt-end delete-btn"
                                        onclick="deleteBlogComment({{ $comment->id }}, '{{ route('admin.blogs.destroyComment', $comment->id) }}')"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </div>
                            {{ $comment->comment }}

                            {{-- Replies --}}
                            @if (!empty($comment->replies) && $comment->replies->count())
                                @foreach ($comment->replies as $reply)
                                    <div class="d-flex mt-3" id="comment-row-id-{{ $reply->id }}">
                                        <a class="mr-3" href="#">
                                            <img src="{{ $reply->user->image ?? asset('adminassets/images/avatars/avatar-3.png') }}"
                                                class="rounded-circle p-1 border" width="90" height="90"
                                                alt="...">
                                        </a>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="row justify-content-between setting-input-group">
                                                <div class="col-lg-6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h5 class="mt-0">{{ $reply->user->name ?? 'Anonymous' }}</h5>
                                                        {!! defaultBadge(strtoupper($reply->status), 'auto') !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 text-end d-flex">
                                                    @if ($reply->status == 'pending')
                                                        <div id="comment-status-btn-id-{{$reply->id}}">
                                                            <button class="btn btn-light btn-sm taxt-end delete-btn" title="Approve"
                                                                onclick="updateStatus({{ $reply->id }}, '{{ route('admin.blogs.updateCommentStatus', [$reply->id, 'status' => 'approved']) }}')"><i
                                                                    class="lni lni-checkmark-circle"></i></button>
                                                            <button class="btn btn-light btn-sm taxt-end delete-btn" title="Reject"
                                                                onclick="updateStatus({{ $reply->id }}, '{{ route('admin.blogs.updateCommentStatus', [$reply->id, 'status' => 'rejected']) }}')"><i
                                                                    class="lni lni-cross-circle"></i></button>
                                                        </div>
                                                    @endif
                                                    <button class="btn btn-light btn-sm taxt-end delete-btn"
                                                        onclick="deleteBlogComment({{ $reply->id }}, '{{ route('admin.blogs.destroyComment', $reply->id) }}')"><i
                                                            class="bx bx-trash"></i></button>
                                                </div>
                                            </div>
                                            {{ $reply->comment }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <hr>
                @empty
                    <p>No comments found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>


{{-- <div class="card">
    <div class="card-body">
        <h6 class="mb-0 text-uppercase">Comments</h6>
        <hr>
        <div class="card radius-10 overflow-auto" style="height: 500px;">
            <div class="card-body">
                <div class="d-flex">
                    <img src="{{ asset('adminassets/images/avatars/avatar-2.png') }}" class="rounded-circle p-1 border"
                        width="90" height="90" alt="...">
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                        vulputate fringilla
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex">
                    <img src="{{ asset('adminassets/images/avatars/avatar-2.png') }}" class="rounded-circle p-1 border"
                        width="90" height="90" alt="...">
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                        vulputate fringilla
                    </div>
                </div>
                <hr>
                <div class="d-flex">
                    <img src="{{ asset('adminassets/images/avatars/avatar-2.png') }}" class="rounded-circle p-1 border"
                        width="90" height="90" alt="...">
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                        vulputate fringilla
                    </div>
                </div>
                <hr>
                <div class="d-flex">
                    <img src="{{ asset('adminassets/images/avatars/avatar-2.png') }}" class="rounded-circle p-1 border"
                        width="90" height="90" alt="...">
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                        vulputate fringilla
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex">
                    <img src="{{ asset('adminassets/images/avatars/avatar-2.png') }}"
                        class="rounded-circle p-1 border" width="90" height="90" alt="...">
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                        vulputate fringilla
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex">
                    <img src="{{ asset('adminassets/images/avatars/avatar-2.png') }}"
                        class="rounded-circle p-1 border" width="90" height="90" alt="...">
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
                        vulputate fringilla
                        <div class="d-flex mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ asset('adminassets/images/avatars/avatar-3.png') }}"
                                    class=" rounded-circle p-1 border" width="90" height="90" alt="...">
                            </a>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
                                condimentum nunc ac nisi vulputate fringilla
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
