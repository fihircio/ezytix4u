@extends('eventmie::o_dashboard.index')

{{-- Page title --}}
@section('title')
    @lang('eventmie-pro::em.luckydraw')
@endsection

@section('o_dashboard')
<section>
<div id="lucky-draw" class="tab-pane">
    <div class="panel-group">
      <div class="panel panel-default lgx-panel">
        <div class="panel-heading px-5">
          <h3>{{ trans('em.lucky_draw') }}</h3>
          <p class="mb-0">{{ trans('em.lucky_draw_info') }}</p>
          <form class="form-horizontal" ref="form" :action="submitUrl()" @submit.prevent="validateForm" method="POST">
            <input type="hidden" name="_token" id="csrf-token" :value="csrf_token" />
            <div class="form-group row mt-3">
              <label class="col-md-3 form-label">{{ trans("em.select_event") }}*</label>
              <div class="col-md-9">
                <select class="form-control" name="event_id" v-model="event_id" v-validate="'required|decimal|is_not:0'" @change="isDirty()">
                  <option value="0">-- {{ trans('em.event') }} --</option>
                  <option v-for="(event, index) in events" :key="index" :value="event.id">{{ event.title }}</option>
                </select>
                <span v-show="errors.has('event_id')" class="help text-danger">{{ errors.first("event_id") }}</span>
              </div>
            </div>
            <div class="form-group row mt-3">
              <div class="col-md-9 offset-md-3">
                <button class="btn btn-primary" type="submit">
                  <i class="fas fa-sd-card"></i>
                  {{ trans("em.draw") }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection