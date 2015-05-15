$(function(){
  var hospitalId = parseInt($("[name='hospital_id']:checked").val());
  $("[name='hospital_id']").on('click', function(){
    newHospitalId = parseInt($("[name='hospital_id']:checked").val());
    if (newHospitalId != hospitalId) {
      hospitalId = newHospitalId;
      $.post('/admin/hospital-department/parent-list', {'hospital_id':hospitalId}, function(json){
        if (json.error_code == 0) {
          var str = '<select class="selectboxit" name="parent_id"><option value="0">--无--</option>';
          $.each(json.list, function(k,v){
            str += '<option value="'+v.id+'">'+v.name+'</option>';
          })
          str += '</select>';
          $("[name='parent_id']").parent().html(str);
          var $this = $("[name='parent_id']");
          opts = {
            showFirstOption: attrDefault($this, 'first-option', true),
            'native': attrDefault($this, 'native', false),
            defaultText: attrDefault($this, 'text', ''),
          };
          $this.addClass('visible');
          $this.selectBoxIt(opts);
          
        }
      },'json');
    }
  })
})