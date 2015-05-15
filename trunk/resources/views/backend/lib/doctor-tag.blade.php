<div class="form-group">
  <label class="col-sm-2 control-label">选择标签</label>
  <div class="col-sm-6 panel panel-default" style="padding-top:15px;margin-left: 1em;margin-top: 1em;">
      <?php $tagList = App\Models\Tag::getTagList(); ?>
      <?php $doctorIds = App\Models\DoctorTag::getTagIdList(Request::Input('doctor_id', 0)); ?>
      <?php $tags    = Input::old('tags', $doctorIds)?>
      <div class="panel panel-default">
        <div class="col-sm-12" style="clear:both;">
          @foreach($tagList as $k => $v)
          <label style="display:inline-block"><span><input type="checkbox" style="margin-right:5px;" name="tags[]" value="{{ $v->id }}" text="{{ $v->name }}" @if(in_array($v->id, $tags)) checked @endif />{{ $v->name }}</span></label>
          @endforeach
        </div>
      </div>
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label">已选标签</label>
  <div class="col-sm-6 panel panel-default" style="padding-top:15px;margin-left: 1em;margin-top: 1em;">
        <div class="col-sm-12  alert alert-warning" style="clear:both;" role="alert">
            <a href="javascript:void(0);" class="alert-link"></a>
        </div>
  </div>
</div>
<script>
  $(function(){
    function showCheckedTag()
    {
      var str = [];
      $("[name='tags[]']:checked").each(function(k, v){
        str[k] = $(v).attr('text');
      })
      $(".alert-link").html(str.join('、'));
      if (str.length == 0) {
        $(".alert-link").html('没有选择标签~');
      }
    }
    showCheckedTag();
    $("[name='tags[]']").on('click', function(){
      showCheckedTag();
    })
  })
</script>