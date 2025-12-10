<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset(getSetting('site icon', 'adminassets/images/favicon.png')) }}" class="logo-icon"
                alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">{{ getSetting('site name', 'Hijabster') }}</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-circle'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

         <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='fadeIn animated bx bx-folder-plus'></i>
                </div>
                <div class="menu-title">CMS</div>
            </a>
            <ul>
                <li> <a href="javascript:;"
                        onclick="showAjaxModal('Create New Page', 'Create', `{{ route('admin.cms.page.create') }}`)"><i
                            class="bx bx-right-arrow-alt"></i>Create Page</a></li>
                @foreach (getCmsPage() as $pages)
                    <li> <a href="{{ route('admin.cms.index', ['slug' => $pages->page_slug]) }}"><i
                                class="bx bx-right-arrow-alt"></i>{{ $pages->page_title }}</a></li>
                @endforeach
            </ul>
        </li> 

         <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='fadeIn animated bx bx-cart'></i>
                </div>
                <div class="menu-title">Inventory</div>
            </a>
            <ul>
                 <li> <a href="{{ route('admin.inventory.brand.index') }}"><i class="bx bx-right-arrow-alt"></i>Brands</a></li> 
                <li> <a href="{{ route('admin.inventory.category.index') }}"><i
                            class="bx bx-right-arrow-alt"></i>Category</a></li>
                 <li> <a href="{{ route('admin.inventory.attributes.index') }}"><i class="bx bx-right-arrow-alt"></i>Attributes & Option</a></li> 
                <li> <a href="{{ route('admin.inventory.product.create') }}"><i
                            class="bx bx-right-arrow-alt"></i>Create Product</a></li>
                <li> <a href="{{ route('admin.inventory.product.index') }}"><i
                            class="bx bx-right-arrow-alt"></i>Products</a></li>
            </ul>
        </li> 


        <li>
            <a href="{{ route('admin.settings') }}">
                <div class="parent-icon"><i class='bx bx-wrench'></i>
                </div>
                <div class="menu-title">Settings</div>
            </a>
        </li>

         <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='fadeIn animated bx bx-image'></i>
                </div>
                <div class="menu-title">Blogs</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.blogs.index') }}"><i class="bx bx-right-arrow-alt"></i>Blogs List</a></li>
                <li> <a href="{{ route('admin.tags.index') }}"><i class="bx bx-right-arrow-alt"></i>Tags</a></li>
            </ul>
        </li> 


        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='fadeIn animated bx bx-paper-plane'></i>
                </div>
                <div class="menu-title">Inquiries</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.contact-inquiry.index') }}">
                        <div class="parent-icon"><i class='bx bx-cookie'></i>
                        </div>
                    <div class="menu-title">Contact Inquries</div>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.users.index') }}">
                      <div class="parent-icon"><i class='bx bx-user'></i>
                      </div>
                      <div class="menu-title">Users</div>
                  </a>
                </li>
                 <li>
                    <a href="{{ route('admin.newsletter-management.index') }}">
                        <div class="parent-icon"><i class='bx bx-cookie'></i>
                        </div>
                        <div class="menu-title">Newsletter Management</div>
                    </a>
                </li>
            </ul>
        </li>


    </ul>
</div>
