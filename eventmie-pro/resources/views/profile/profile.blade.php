@extends('eventmie::layouts.app')

@section('title')
    @lang('eventmie-pro::em.profile')
@endsection

@section('content')
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-12">
                <div class="bg-gray-200 rounded-3 px-3 mb-2">
                    <ul class="nav nav-lb-tab text-center w-space">
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'personal-details' }">
                                @lang('eventmie-pro::em.personal_details')
                                <i class="fas fa-exclamation-circle text-danger" v-if="!store.state.personal_details"></i>
                                <i class="fas fa-check-circle text-primary" v-else></i>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'security' }">
                                @lang('eventmie-pro::em.security')
                                <i class="fas fa-check-circle text-primary"></i>
                            </router-link>
                        </li>
                        
                        @if (!Auth::user()->hasRole('customer'))
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'bank-details' }">
                                @lang('eventmie-pro::em.update_bank_details')
                                <i class="fas fa-exclamation-circle text-danger" v-if="!store.state.update_bank_details"></i>
                                <i class="fas fa-check-circle text-primary" v-else></i>
                            </router-link>
                        </li>
                        @endif

                        
                        @if (Auth::user()->hasRole('organiser'))


                            <li class="nav-item" >
                                <router-link class="nav-link" :to="{ name: 'organiser-info' }">
                                    @lang('eventmie-pro::em.organiser_info')
                                    <i class="fas fa-exclamation-circle text-danger" v-if="!store.state.organiser_info"></i>
                                    <i class="fas fa-check-circle text-primary" v-else></i>
                                </router-link>
                            </li>
                        @endif
                        
                        @if (Auth::user()->hasRole('customer'))
                            
                            @if ((int)setting('multi-vendor.multi_vendor'))
                                <li class="nav-item">
                                    <router-link class="nav-link" :to="{ name: 'become-organiser' }">
                                        @lang('eventmie-pro::em.become_organiser') 
                                        <i class="fas fa-person-booth"></i>
                                    </router-link>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <router-view>
                        </router-view>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    var multi_vendor = {!! json_encode(setting('multi-vendor.manually_approve_organizer'), JSON_HEX_APOS) !!};
    var user = {!! json_encode($user, JSON_HEX_APOS) !!};
    var csrf_token = {!! json_encode(@csrf_token(), JSON_HEX_APOS) !!};
    var manually_approve_organizer =  {!! json_encode((int)setting('multi-vendor.manually_approve_organizer'), JSON_HEX_APOS) !!};
    var is_organiser =  {!! json_encode(Auth::user()->hasRole('organiser'), JSON_HEX_APOS) !!};
</script>
<script type="text/javascript" src="{{ eventmie_asset('js/profile.js') }}"></script>
@stop
