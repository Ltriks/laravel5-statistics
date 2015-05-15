<?php use App\Models\DepartmentDoctor; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $departmentDoctors = DepartmentDoctor::getDepartments();?>
<?php $list = HospitalDepartment::getDepartmentList(Request::Input('hospital_id'));?>
@if(count($list))
<div class="form-group">
  <label class="col-sm-2 control-label"><i class="required-star">*</i>科室</label>
  <div class="col-sm-3">
  <?php $departments = Input::old('departments', $departmentDoctors) ?>
    <select class="selectboxit" name="departments[]">
        @foreach($list as $k => $v)
            <option value='{{ $v['id'] }}' @if(in_array($v['id'], $departments)) selected @endif>{{ $v['name'] }}</option>
            @foreach($v['list'] as $k2 => $v2)
            <option value='{{ $v2['id'] }}' @if(in_array($v2['id'], $departments)) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;{{ $v2['name'] }}</option>
            @endforeach

        @endforeach
    </select> 
  </div>
</div>
@endif