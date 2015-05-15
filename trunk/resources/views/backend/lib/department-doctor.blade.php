<?php use App\Models\DepartmentDoctor; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $departmentDoctors = DepartmentDoctor::getDepartments();?>
<?php $list = HospitalDepartment::getDepartmentList(Request::Input('hospital_id'));?>
@if(count($list))
<div class="form-group">
  <label class="col-sm-2 control-label"><i class="required-star">*</i>选择科室</label>
  <div class="col-sm-6 panel panel-default" style="padding-top:15px;margin-left: 1em;margin-top: 1em;">
      @foreach($list as $k => $v)
      <div class="panel panel-default">
        <div class="col-sm-4">
          <label class="control-label">{{ $v['name'] }}</label>
        </div>
        <div class="col-sm-12" style="clear:both;">
          <?php $departments = Input::old('departments', $departmentDoctors) ?>
          @foreach($v['list'] as $k2 => $v2)
          <label style="display:inline-block"><span><input type="checkbox" style="margin-right:5px;" name="departments[]" value="{{ $v2['id'] }}" @if(in_array($v2['id'], $departments)) checked @endif />{{ $v2['name'] }}</span></label>
          @endforeach
        </div>
      </div>
      @endforeach
  </div>
</div>
@endif