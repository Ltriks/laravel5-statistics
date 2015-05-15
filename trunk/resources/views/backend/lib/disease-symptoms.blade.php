<?php use App\Models\Category; ?>
<?php use App\Models\DepartmentDisease; ?>
<?php $departmentDiseases = DepartmentDisease::getDepartmentDiseaseList();?>
<?php $list = Category::getDiseaseSymptomsOption();?>
@if(count($list))
<div class="form-group">
  <label class="col-sm-2 control-label"><i class="required-star">*</i>选择病症</label>
  <div class="col-sm-6 panel panel-default" style="padding-top:15px;margin-left: 1em;margin-top: 1em;">
      @foreach($list as $k => $v)
      <div class="panel panel-default">
        <div class="col-sm-4">
          <label class="control-label">{{ $v['name'] }}&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" style="margin-right:5px;" class="select-children-all" value=""></label>
        </div>
        <div class="col-sm-12" style="clear:both;">
          <?php $disease_symptoms = Input::old('disease_symptoms', $departmentDiseases) ?>
          @foreach($v['list'] as $k2 => $v2)
          <label style="display:inline-block"><span><input type="checkbox" style="margin-right:5px;" name="disease_symptoms[]" value="{{ $v2['id'] }}" @if(in_array($v2['id'], $disease_symptoms)) checked @endif />{{ $v2['name'] }}</span></label>
          @endforeach
        </div>
      </div>
      @endforeach
  </div>
</div>
@endif
<script type="text/javascript">
  $(function(){
    $(".select-children-all").on('click', function(){
      var checkList = $(this).parent().parent().next().find('[type="checkbox"]');
      if (checkList.first().prop('checked')) {
        checkList.prop('checked', false);
      } else {
        checkList.prop('checked', true);
      }
      
    })
  })
</script>