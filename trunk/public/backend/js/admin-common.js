/**
 *   Main JavaScript File
 *
 *  Theme by: www.laborator.co
 **/

var public_vars = public_vars || {};

/**
 * 用于自动完成时下拉列表的用户展示
 *
 * @param {Object} user
 *
 * @return {Function}
 */
var userTpl = function(user){
               var string =  [
                                '<p class="">',
                                '<span>ID:',user.id,'</span>',
                                '<span> <strong>', user.username, '(', user.mobile ,')</strong></span>',
                                '</p>'
                              ].join('');

                return string;
              };
              
;(function($, window, undefined){
  
  "use strict";
  
  $(document).ready(function()
  {
    // Sidebar Menu var
    public_vars.$body     = $("body");
    public_vars.$pageContainer  = public_vars.$body.find(".page-container");
    public_vars.$sidebarMenu  = public_vars.$pageContainer.find('.sidebar-menu');
    public_vars.$mainMenu     = public_vars.$sidebarMenu.find('#main-menu');
    public_vars.$mainContent  = public_vars.$pageContainer.find('.main-content');
    
    public_vars.$body.addClass('loaded');
    
    // Just to make sure...
    $(window).on('error', function(ev)
    { 
      // Do not let page without showing if JS fails somewhere
      //init_page_transitions();
    });
        
    // Sidebar Menu Setup
    setup_sidebar_menu();
    
    // Sidebar Collapse icon
    public_vars.$sidebarMenu.find(".sidebar-collapse-icon").on('click', function(ev)
    {
      ev.preventDefault();
      
      var with_animation = $(this).hasClass('with-animation');
      
      toggle_sidebar_menu(with_animation);
    });

    // Mobile Sidebar Collapse icon
    public_vars.$sidebarMenu.find(".sidebar-mobile-menu a").on('click', function(ev)
    {
      ev.preventDefault();
      var with_animation = $(this).hasClass('with-animation');
      if(with_animation)
      {
        public_vars.$mainMenu.stop().slideToggle('normal', function()
        {
          public_vars.$mainMenu.css('height', 'auto');
        });
      }
      else
      {
        public_vars.$mainMenu.toggle();
      }
    });
    
    // Close Sidebar if Tablet Screen is visible
    public_vars.$sidebarMenu.data('initial-state', (public_vars.$pageContainer.hasClass('sidebar-collapsed') ? 'closed' : 'open'));
    if(is('tabletscreen'))
    {
      hide_sidebar_menu(false);
    }
  
    // NiceScroll
    if($.isFunction($.fn.niceScroll))
    {
      var nicescroll_defaults = {
        cursorcolor: '#d4d4d4',
        cursorborder: '1px solid #ccc',
        railpadding: {right: 3},
        cursorborderradius: 1,
        autohidemode: true,
        sensitiverail: true
      };
      
      var fixed_sidebar = $(".sidebar-menu.fixed");
      
      if(fixed_sidebar.length == 1)
      {
        var fs_tm = 0;
        
        fixed_sidebar.niceScroll({
          cursorcolor: '#454a54',
          cursorborder: '1px solid #454a54',
          railpadding: {right: 3},
          railalign: 'right',
          cursorborderradius: 1
        });
        
        fixed_sidebar.on('click', 'li a', function()
        { 
          fixed_sidebar.getNiceScroll().resize();
          fixed_sidebar.getNiceScroll().show();
          
          window.clearTimeout(fs_tm);
          
          fs_tm = setTimeout(function()
          {           
            fixed_sidebar.getNiceScroll().resize();
          }, 500);
        });
      }
    }
    
    // Panels
    // Added on v1.1.4 - Fixed collapsing effect with panel tables
    $(".panel-heading").each(function(i, el)
    {
      var $this = $(el),
        $body = $this.next('table');
      $body.wrap('<div class="panel-body with-table"></div>');
      $body = $this.next('.with-table').next('table');
      $body.wrap('<div class="panel-body with-table"></div>');
      
    });
    
    continueWrappingPanelTables();

    // End of: Added on v1.1.4
    $('body').on('click', '.panel > .panel-heading > .panel-options > a[data-rel="reload"]', function(ev)
    {
      ev.preventDefault();
      
      var $this = jQuery(this).closest('.panel');
      
      blockUI($this);
      $this.addClass('reloading');
      
      setTimeout(function()
      {
        unblockUI($this)
        $this.removeClass('reloading');
      }, 900);
      
    }).on('click', '.panel > .panel-heading > .panel-options > a[data-rel="close"]', function(ev)
    {
      ev.preventDefault();
      var $this = $(this),
        $panel = $this.closest('.panel');
      var t = new TimelineLite({
        onComplete: function()
        {
          $panel.slideUp(function()
          {
            $panel.remove();
          });
        }
      });
      
      t.append( TweenMax.to($panel, .2, {css: {scale: 0.95}}) );
      t.append( TweenMax.to($panel, .5, {css: {autoAlpha: 0, transform: "translateX(100px) scale(.95)"}}) );
      
    }).on('click', '.panel > .panel-heading > .panel-options > a[data-rel="collapse"]', function(ev)
    {
      ev.preventDefault();
      
      var $this = $(this),
        $panel = $this.closest('.panel'),
        $body = $panel.children('.panel-body, .table'),
        do_collapse = ! $panel.hasClass('panel-collapse');
      
      if($panel.is('[data-collapsed="1"]'))
      {
        $panel.attr('data-collapsed', 0);
        $body.hide();
        do_collapse = false;
      }
      
      if(do_collapse)
      {
        $body.slideUp('normal', fit_main_content_height);
        $panel.addClass('panel-collapse');
      }
      else
      {       
        $body.slideDown('normal', fit_main_content_height);
        $panel.removeClass('panel-collapse');
      }
    });
    
    // Popovers and tooltips
    $('[data-toggle="popover"]').each(function(i, el)
    {
      var $this = $(el),
        placement = attrDefault($this, 'placement', 'right'),
        trigger = attrDefault($this, 'trigger', 'click'),
        popover_class = $this.hasClass('popover-secondary') ? 'popover-secondary' : ($this.hasClass('popover-primary') ? 'popover-primary' : ($this.hasClass('popover-default') ? 'popover-default' : ''));
      
      $this.popover({
        placement: placement,
        trigger: trigger
      });

      $this.on('shown.bs.popover', function(ev)
      {
        var $popover = $this.next();
        
        $popover.addClass(popover_class);
      });
    });
    
    $('[data-toggle="tooltip"]').each(function(i, el)
    {
      var $this = $(el),
        placement = attrDefault($this, 'placement', 'top'),
        trigger = attrDefault($this, 'trigger', 'hover'),
        popover_class = $this.hasClass('tooltip-secondary') ? 'tooltip-secondary' : ($this.hasClass('tooltip-primary') ? 'tooltip-primary' : ($this.hasClass('tooltip-default') ? 'tooltip-default' : ''));
      
      $this.tooltip({
        placement: placement,
        trigger: trigger
      });

      $this.on('shown.bs.tooltip', function(ev)
      {
        var $tooltip = $this.next();
        
        $tooltip.addClass(popover_class);
      });
    });
    
  //dateranger
    if ($.isFunction($.fn.daterangepicker))
    {
        initTimeRangePicker('.daterange');
    }

    
    // SelectBoxIt Dropdown replacement
    if($.isFunction($.fn.selectBoxIt))
    {
      $("select.selectboxit").each(function(i, el)
      {
        var $this = $(el),
          opts = {
            showFirstOption: attrDefault($this, 'first-option', true),
            'native': attrDefault($this, 'native', false),
            defaultText: attrDefault($this, 'text', ''),
          };
          
        $this.addClass('visible');
        $this.selectBoxIt(opts);
      });
    }
    
    
    // Auto Size for Textarea
    if($.isFunction($.fn.autosize))
    {
      $("textarea.autogrow, textarea.autosize").autosize();
    }
    
    // Tagsinput
    if($.isFunction($.fn.tagsinput))
    {
      $(".tagsinput").tagsinput();
    }
    
    
    // Datepicker
    if($.isFunction($.fn.datepicker))
    {
      $(".datepicker").each(function(i, el)
      {
        var $this = $(el),
          opts = {
            format: attrDefault($this, 'format', 'mm/dd/yyyy'),
            startDate: attrDefault($this, 'startDate', ''),
            endDate: attrDefault($this, 'endDate', ''),
            daysOfWeekDisabled: attrDefault($this, 'disabledDays', ''),
            startView: attrDefault($this, 'startView', 0),
            rtl: rtl()
          },
          $n = $this.next(),
          $p = $this.prev();
                
        $this.datepicker(opts);
        
        if($n.is('.input-group-addon') && $n.has('a'))
        {
          $n.on('click', function(ev)
          {
            ev.preventDefault();
            
            $this.datepicker('show');
          });
        }
        
        if($p.is('.input-group-addon') && $p.has('a'))
        {
          $p.on('click', function(ev)
          {
            ev.preventDefault();
            
            $this.datepicker('show');
          });
        }
      });
    }
    
    // Timepicker
    if($.isFunction($.fn.timepicker))
    {
      $(".timepicker").each(function(i, el)
      {
        var $this = $(el),
          opts = {
            template: attrDefault($this, 'template', false),
            showSeconds: attrDefault($this, 'showSeconds', false),
            defaultTime: attrDefault($this, 'defaultTime', 'current'),
            showMeridian: attrDefault($this, 'showMeridian', true),
            minuteStep: attrDefault($this, 'minuteStep', 15),
            secondStep: attrDefault($this, 'secondStep', 15)
          },
          $n = $this.next(),
          $p = $this.prev();
        
        $this.timepicker(opts);
        
        if($n.is('.input-group-addon') && $n.has('a'))
        {
          $n.on('click', function(ev)
          {
            ev.preventDefault();
            
            $this.timepicker('showWidget');
          });
        }
        
        if($p.is('.input-group-addon') && $p.has('a'))
        {
          $p.on('click', function(ev)
          {
            ev.preventDefault();
            
            $this.timepicker('showWidget');
          });
        }
      });
    }
    
  
    // Modal Static
    public_vars.$body.on('click', '.modal[data-backdrop="static"]', function(ev)
    {
      var $modal_dialog = $(this).find('.modal-dialog .modal-content');
      
      var tt = new TimelineMax({paused: true});
      
      tt.append( TweenMax.to($modal_dialog, .1, {css: {scale: 1.1}, ease: Expo.easeInOut}) );
      tt.append( TweenMax.to($modal_dialog, .3, {css: {scale: 1}, ease: Back.easeOut}) );
      
      tt.play();
    });
    
    // Fit main content height
    fit_main_content_height();
    
    var fmch = 0,
      fmch_fn = function(){
      
      window.clearTimeout(fmch);
      fit_main_content_height();
      
      fmch = setTimeout(fmch_fn, 800);
    };
    
    fmch_fn();

  //全选
    $('input.select-all').change(function(){
      var targetObj = $($(this).data('target'));
      var status = $(this).prop('checked');
      targetObj.prop('checked', status);
    });

    //权限页面的全选按钮
    if($('.select-all-permission').length) {
      if ($('.permissions .chkbox:checked').length == $('.permissions .chkbox').length) {
        $('.select-all-permission .select-all').attr('checked', true);
      };
    }
    
    //表格filter自动提交
    $('.auto-submit').on('change', function(){
      $($(this).data('target')).click();
    });

    //批量操作按钮
    $('.btn-do-more').on('click', function(e){
      e.preventDefault();
      if (!$('.chkbox:checked').length) {
        alert('请勾选要操作的对象！');
        return false;
      };
      if ($('.table-actions [name="action"]').val() == '') {
        alert('请选择批量操作类型');
        return false;
      };
      if (!confirm('确认执行当前操作？')) {
        return false;
      };
      $('.btn-do-more').closest("form").submit()
    });

    $('table tr td.do').each(function(){
      if (/^\s+$/.test($(this).text())) {
        $(this).html('无可用操作');
      };
    });
    
    // 图片上传框的值改变后修改对应预览图片的src
    $('.url-image').on('change', function(){
      var _preview = $($(this).data('preview'));
      !_preview.data('old-src') || _preview.attr('data-old-src', _preview.attr('src'));
      if (!$(this).val()) {
        _preview.attr('src', _preview.data('old-src'));
        return;
      };

      if ($(this).data('preview')) {
        $($(this).data('preview')).attr('src', $(this).val()).css({'height':'auto', 'width':'auto', 'max-height':'200px'});
      };

    });
    // 添加编辑题题目自动处理
    function accountFontNum()
    {
      var claim = $("[name='claim']").val();
      var two_star_scale = $("[name='two_star_scale']").val();
      var three_star_scale = $("[name='three_star_scale']").val();
      
      if (isNaN(claim)) {
        return false;
      }
      if (isNaN(two_star_scale)) {
        return false;
      }
      if (isNaN(three_star_scale)) {
        return false;
      }
      var two_star_scale_num = accMul(claim, accDiv(two_star_scale, 100));
      var three_star_scale_num = accMul(claim, accDiv(three_star_scale, 100));
      $("#two_star_scale_num").html(two_star_scale_num);
      $("#three_star_scale_num").html(three_star_scale_num);
    }
    $("[name='claim']").on('change', function(){
      accountFontNum();
    });
    $("[name='two_star_scale']").on('change', function(){
      accountFontNum();
    });
    $("[name='three_star_scale']").on('change', function(){
      accountFontNum();
    });
    
    // 编辑时图片预览(页面初始化执行哪些操作)
    setTimeout(function(){
        $('.url-image').each(function(){
            $(this).change();
        });
        accountFontNum();
    }, 30);
    
    // typeahead搜索按钮
    $('.typeahead-search-btn').each(function(){
      var $this = $(this);
      var targetInput = $($this.data('target'));

      targetInput.slideUp();
      $this.on('click', function(){
        if (targetInput.is(':visible')) {
          $this.find('span').text('搜索');
          targetInput.val('').slideUp(0);
        } else {
          $this.find('span').text('收起');
          targetInput.val('').slideDown(0);
        }
        
      });
    });
    // 类型对象的placeholder切换,比如banner创建页，消息创建页
    $('.placeholder-changer').each(function(i, el){
      var $this = $(el);
      var targetInput = $($this.data('placeholder-target'));
      var defaultPlaceholder = targetInput.attr('placeholder');
      $this.on('change', function(){
        var placeholder;
        if (!$this.val().length) {
          placeholder = defaultPlaceholder;
        } else {
          placeholder = $this.find('option[value="'+$this.val()+'"]').data('placeholder');
        }
        targetInput.attr('placeholder', placeholder);
      });
    });

  });
  
  //tips
  $('.tips i').click(function(){
    $(this).parent().hide(600, function(){ $(this).remove()});
  });
  $('.tips').each(function(){
    var _this = $(this);
    setTimeout(function(){
      _this.slideUp(800, function(){ $(this).remove() });
    }, 20000);
  });

  // Enable/Disable Resizable Event
  var wid = 0;
  
  $(window).resize(function() {
    clearTimeout(wid);
    wid = setTimeout(trigger_resizable, 200);
  });

  
  
})(jQuery, window);


/* Functions */
function fit_main_content_height()
{
  if(public_vars.$sidebarMenu.length && public_vars.$sidebarMenu.hasClass('fixed') == false)
  {
    public_vars.$sidebarMenu.css('min-height', '');
    public_vars.$mainContent.css('min-height', '');
    
    if(isxs())
    { 
      if(typeof reset_mail_container_height != 'undefined')
        reset_mail_container_height();
      return;
      
      if(typeof fit_calendar_container_height != 'undefined')
        reset_calendar_container_height();
      return;
    }
    
    var sm_height  = public_vars.$sidebarMenu.outerHeight(),
      mc_height  = public_vars.$mainContent.outerHeight(),
      doc_height = $(document).height(),
      win_height = $(window).height();
    
    if(win_height > doc_height)
    {
      doc_height = win_height;
    }
      
    
    public_vars.$mainContent.css('min-height', doc_height);
    public_vars.$sidebarMenu.css('min-height', doc_height);
    //public_vars.$chat.css('min-height', doc_height);
    
    if(typeof fit_mail_container_height != 'undefined')
      fit_mail_container_height();
    
    if(typeof fit_calendar_container_height != 'undefined')
      fit_calendar_container_height();
  }
}


// Sidebar Menu Setup
function setup_sidebar_menu()
{
  var $items_with_submenu   = public_vars.$sidebarMenu.find('li:has(ul)'),
    submenu_options     = {
      submenu_open_delay: 0.5,
      submenu_open_easing: Sine.easeInOut,
      submenu_opened_class: 'opened'
    },
    root_level_class    = 'root-level',
    is_multiopen      = public_vars.$mainMenu.hasClass('multiple-expanded');
  
  public_vars.$mainMenu.find('> li').addClass(root_level_class);
  
  $items_with_submenu.each(function(i, el)
  {
    var $this = $(el),
      $link = $this.find('> a'),
      $submenu = $this.find('> ul');
    
    $this.addClass('has-sub');
    
    $link.click(function(ev)
    {
      ev.preventDefault();
      
      if( ! is_multiopen && $this.hasClass(root_level_class))
      {
        var close_submenus = public_vars.$mainMenu.find('.' + root_level_class).not($this).find('> ul');
        
        close_submenus.each(function(i, el)
        {
          var $sub = $(el);
          menu_do_collapse($sub, $sub.parent(), submenu_options);
        });
      }
      
      if( ! $this.hasClass(submenu_options.submenu_opened_class))
      {
        var current_height;
        
        if( ! $submenu.is(':visible'))
        {
          menu_do_expand($submenu, $this, submenu_options);
        }
      }
      else
      {
        menu_do_collapse($submenu, $this, submenu_options);
      }
      
      fit_main_content_height();
    });

  });
  
  // Open the submenus with "opened" class
  public_vars.$mainMenu.find('.'+submenu_options.submenu_opened_class+' > ul').addClass('visible');
  
  // Well, somebody may forgot to add "active" for all inhertiance, but we are going to help you (just in case) - we do this job for you for free :P!
  if(public_vars.$mainMenu.hasClass('auto-inherit-active-class'))
  {
    menu_set_active_class_to_parents( public_vars.$mainMenu.find('.active') );
  }
  
  // Search Input
  var $search_input = public_vars.$mainMenu.find('#search input[type="text"]'),
    $search_el = public_vars.$mainMenu.find('#search');
    
  public_vars.$mainMenu.find('#search form').submit(function(ev)
  {
    var is_collapsed = public_vars.$pageContainer.hasClass('sidebar-collapsed');
    
    if(is_collapsed)
    {
      if($search_el.hasClass('focused') == false)
      {
        ev.preventDefault();
        $search_el.addClass('focused');
        
        $search_input.focus();
        
        return false;
      }
    }
  });
  
  $search_input.on('blur', function(ev)
  {
    var is_collapsed = public_vars.$pageContainer.hasClass('sidebar-collapsed');
    
    if(is_collapsed)
    {
      $search_el.removeClass('focused');
    }
  });
  
  
  // Collapse Icon (mobile device visible)
  var show_hide_menu = $('');
  
  public_vars.$sidebarMenu.find('.logo-env').append(show_hide_menu);
}


function menu_do_expand($submenu, $this, options)
{
  $submenu.addClass('visible').height('');
  current_height = $submenu.outerHeight();
  
  var props_from = {
    opacity: .2, 
    height: 0, 
    top: -20
  },
  props_to = {
    height: current_height, 
    opacity: 1, 
    top: 0
  };
  
  if(isxs())
  {
    delete props_from['opacity'];
    delete props_from['top'];
    
    delete props_to['opacity'];
    delete props_to['top'];
  }
  
  TweenMax.set($submenu, {css: props_from});

  $this.addClass(options.submenu_opened_class);
  
  TweenMax.to($submenu, options.submenu_open_delay, {css: props_to, ease: options.submenu_open_easing, onComplete: function()
  {
    $submenu.attr('style', '');
    fit_main_content_height();
  }});
}


function menu_do_collapse($submenu, $this, options)
{
  if(public_vars.$pageContainer.hasClass('sidebar-collapsed') && $this.hasClass('root-level'))
  {
    return;
  }
  
  $this.removeClass(options.submenu_opened_class);
  
  TweenMax.to($submenu, options.submenu_open_delay, {css: {height: 0, opacity: .2}, ease: options.submenu_open_easing, onComplete: function()
  {
    $submenu.removeClass('visible');
    fit_main_content_height();
  }});
}


function menu_set_active_class_to_parents($active_element)
{
  if($active_element.length)
  {
    var $parent = $active_element.parent().parent();
    
    $parent.addClass('active');
    
    if(! $parent.hasClass('root-level'))
      menu_set_active_class_to_parents($parent)
  }
}


jQuery(public_vars, {
  hover_index: 4
});


// Block UI Helper
function blockUI($el)
{
  $el.block({
    message: '',
    css: {
      border: 'none',
      padding: '0px',
      backgroundColor: 'none'
    },
    overlayCSS: {
      backgroundColor: '#fff',
      opacity: .3,
      cursor: 'wait'
    }
  });
}

function unblockUI($el)
{
  $el.unblock();
}


// Element Attribute Helper
function attrDefault($el, data_var, default_val)
{
  if(typeof $el.data(data_var) != 'undefined')
  {
    return $el.data(data_var);
  }
  
  return default_val;
}

// Root Wizard Current Tab
function setCurrentProgressTab($rootwizard, $nav, $tab, $progress, index)
{
  $tab.prevAll().addClass('completed');
  $tab.nextAll().removeClass('completed');
  
  var items         = $nav.children().length,
    pct           = parseInt((index+1) / items * 100, 10),
    $first_tab    = $nav.find('li:first-child'),
    margin        = (1/(items*2) * 100) + '%';//$first_tab.find('span').position().left + 'px';
  
  if( $first_tab.hasClass('active'))
  {
    $progress.width(0);
  }
  else
  {
      $progress.width( ((index-1) /(items-1)) * 100 + '%' ); //$progress.width( $tab.prev().position().left - $tab.find('span').width()/2 );
  }
  
  
  $progress.parent().css({
    marginLeft: margin,
    marginRight: margin
  });
  
  /*var m = $first_tab.find('span').position().left - $first_tab.find('span').width() / 2;
  
  $rootwizard.find('.tab-content').css({
    marginLeft: m,
    marginRight: m
  });*/
}

// Scroll to Bottom
function scrollToBottom($el)
{
  if(typeof $el == 'string')
    $el = $($el);
    
  $el.get(0).scrollTop = $el.get(0).scrollHeight;
}


// Check viewport visibility (entrie element)
function elementInViewport(el) 
{ 
  var top = el.offsetTop;
  var left = el.offsetLeft;
  var width = el.offsetWidth;
  var height = el.offsetHeight;

  while (el.offsetParent) {
    el = el.offsetParent;
    top += el.offsetTop;
    left += el.offsetLeft;
  }

  return (
    top >= window.pageYOffset &&
    left >= window.pageXOffset &&
    (top + height) <= (window.pageYOffset + window.innerHeight) &&
    (left + width) <= (window.pageXOffset + window.innerWidth)
  );
}

// X Overflow
function disableXOverflow()
{
  public_vars.$body.addClass('overflow-x-disabled');
}

function enableXOverflow()
{
  public_vars.$body.removeClass('overflow-x-disabled');
}


function continueWrappingPanelTables()
{
  var $tables = jQuery(".panel-body.with-table + table");
  
  if($tables.length)
  {
    $tables.wrap('<div class="panel-body with-table"></div>');
    continueWrappingPanelTables();
  }
}

//调用：accAdd(arg1,arg2)   
//返回值：arg1加上arg2的精确结果   
function accAdd(arg1,arg2){  
  var r1,r2,m;  
  try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}  
  try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}  
  m=Math.pow(10,Math.max(r1,r2))  
  return (arg1*m+arg2*m)/m  
}

//说明：javascript的减法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的减法结果。   
//调用：accSub(arg1,arg2)   
//返回值：arg1减上arg2的精确结果   
function accSub(arg1,arg2){      
  return accAdd(arg1,-arg2);  
}

//说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。   
//调用：accMul(arg1,arg2)   
//返回值：arg1乘以arg2的精确结果   
function accMul(arg1,arg2)  
{  
  var m=0,s1=arg1.toString(),s2=arg2.toString();  
  try{m+=s1.split(".")[1].length}catch(e){}  
  try{m+=s2.split(".")[1].length}catch(e){}  
  return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)  
}

//说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。   
//调用：accDiv(arg1,arg2)   
//返回值：arg1除以arg2的精确结果   
function accDiv(arg1,arg2){  
  var t1=0,t2=0,r1,r2;  
  try{t1=arg1.toString().split(".")[1].length}catch(e){}  
  try{t2=arg2.toString().split(".")[1].length}catch(e){}  
  with(Math){  
      r1=Number(arg1.toString().replace(".",""))  
      r2=Number(arg2.toString().replace(".",""))  
      return (r1/r2)*pow(10,t2-t1);  
  }  
}

/**
 * loadding
 * @param status
 */
function loading(status) {
  if (status) {
    $('body').append('<div class="overlayout"><div class="black-layout"></div><div class="progress progress-striped active">'+
              '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">'+
              '操作进行中，请等待...'+
              '</div>'+
              '</div></div>');  
  } else {
    $('.overlayout').fadeOut(300, function(){
      $(this).remove();
    });
  }
  
}

/**
 * 提示消息
 * @param {String} msg 
 * @return {void} 
 */
function alertMsg(msg, color) {
  color = color || 'info';
  $('.message-container').append('<div class="tips text-'+color+'"><i class="pull-right">✕</i>'+msg+'</div>');
  updateTips();
}

//时间范围选择器
function initTimeRangePicker (selector) {
  $(selector).each(function(i, el)
  {
      var ranges = {
          '1天': [moment(), moment().add('days', 1)],
          '5天': [moment(), moment().add('days', 5)],
          '7天': [moment(), moment().add('days', 7)],
          '10天': [moment(), moment().add('days', 10)],
          '15天': [moment(), moment().add('days', 15)],
          '30天': [moment(), moment().add('days', 30)],
          '半年': [moment(), moment().add('months', 6)],
          '1年': [moment(), moment().add('years', 1)],
          '2年': [moment(), moment().add('years', 2)],
          
      };
      var $this = $(el),
      opts = {
          format: attrDefault($this, 'format', 'YYYY-MM-DD HH:mm:ss'),
          timePicker: attrDefault($this, 'timePicker', true),
          timePickerIncrement: attrDefault($this, 'timePickerIncrement', false),
          separator: attrDefault($this, 'separator', ' - '),
          locale: {
            applyLabel: '确定',
            cancelLabel: '取消',
            fromLabel: '从',
            toLabel: '到',
            weekLabel: '周',
            customRangeLabel: '自定义范围',
            daysOfWeek: moment()._lang._weekdaysMin.slice(),
            monthNames: moment()._lang._monthsShort.slice(),
            firstDay: 0
          }

      },
      min_date = attrDefault($this, 'minDate', ''),
      max_date = attrDefault($this, 'maxDate', ''),
      start_date = attrDefault($this, 'startDate', ''),
      end_date = attrDefault($this, 'endDate', '');
      if ($this.hasClass('add-ranges'))
      {
          opts['ranges'] = ranges;
      }
      if (min_date.length)
      {
          opts['minDate'] = min_date;
      }
      if (max_date.length)
      {
          opts['maxDate'] = max_date;
      }
      if (start_date.length)
      {
          opts['startDate'] = start_date;
      }
      if (end_date.length)
      {
          opts['endDate'] = end_date;
      }
      $this.daterangepicker(opts, 
      function(start, end)
      {
          var drp = $this.data('daterangepicker');
          if ($this.is('[data-callback]'))
          {
              callback_test(start, end);
          }
          if ($this.hasClass('daterange-inline'))
          {
              $this.find('span').html(start.format(drp.format) + drp.separator + end.format(drp.format));
          }
      });
  });
}

function updateTips () {
  //tips
  $('.tips i').on('click', function(){
    $(this).parent().hide(600, function(){ $(this).remove()});
  });
  $('.tips').each(function(){
    var _this = $(this);
    setTimeout(function(){
      _this.slideUp(800, function(){ $(this).remove() });
    }, 8000);
  });
}

// 自动完成
if ($.isFunction($.fn.typeahead))
 {
    /**
     * 更多使用方法参考：
     * https://github.com/twitter/typeahead.js
     */
    $(".typeahead").each(function(i, el)
    {
        function extractor(query) {
            console.log(query);
            var result = /([^,]+)$/.exec(query);
            if(result && result[1])
                return result[1].trim();
            return '';
        }

        var $this = $(el),
        opts = {
            name: $this.attr('name') ? $this.attr('name') : ($this.attr('id') ? $this.attr('id') : 'tt'),
            displayKey: $this.attr('displayKey') ? $this.attr('displayKey') : 'label',
        },
        valueKey = $this.attr('valueKey') ? $this.attr('valueKey') : 'id';
        if ($this.hasClass('tagsinput')) return;

        //是否把值放入其它目录
        var $target = $($this.data('target'));

        var Bdopt = {
                  name: $this.attr('name') ? $this.attr('name') : ($this.attr('id') ? $this.attr('id') : 'tt'),
                  datumTokenizer: Bloodhound.tokenizers.obj.whitespace(valueKey),  
                  queryTokenizer: Bloodhound.tokenizers.whitespace
                }

        if ($this.data('local'))
        {
            var local = $this.data('local');
            local = local.replace(/\s*,\s*/g, ',').split(',');
            Bdopt['local'] = local;
        }
        if ($this.data('prefetch'))
        {
            var prefetch = $this.data('prefetch');
            Bdopt['prefetch'] = prefetch;
        }
        if ($this.data('remote'))
        {
            var remote = $this.data('remote');
            Bdopt['remote'] = {
              url:remote,
              replace:function(query){
                    var keyword = $this.val().split(',').pop();
                    return query.replace('%QUERY', keyword);
                  },
              filter: function(response){
                if (!$target || !$target.val().length) {
                  return response;
                };

                var tmp = [];

                response.forEach(function(item){
                  if (!in_array(item[valueKey], $target.val().split(','))) {
                    tmp.push(item);
                  };
                });

                return tmp;
              }};
            
        }
        
        opts['templates'] = {
          suggestion: null,
          empty: function(){return '<p style="padding:0 10px;">'+($this.data('emptyString') || '未找到相关数据') +'</p>';}
        };

        if ( window[$this.data('view-callback')] && (typeof window[$this.data('view-callback')] == 'function')) {
          opts.templates['suggestion'] = window[$this.data('view-callback')];
        };

        var Bd = new Bloodhound(Bdopt);
        Bd.initialize();
        opts['source'] = Bd.ttAdapter();

        $this.typeahead(null, opts).on('typeahead:selected', function(e, data) {
                                //用于多选时把值塞到对象文本框，且逗号隔开
                                if ($target) {
                                  var tvalue = '';
                                  if ($target.val().length) {
                                    tvalue = $target.val().split(' , ');
                                    if (!in_array(data[valueKey], tvalue)) {
                                      tvalue.push(data[valueKey]);
                                    };
                                    
                                  } else {
                                    tvalue = [data[valueKey]];
                                  }
                                  $target.val(tvalue.join(' , '));
                                  $this.val('');
                                };
                              });
    });
}

$(function(){
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("[id^='common_delete_']").on('click', function(){
    if(!confirm('操作不可恢复，确认删除么？')) {
      return true;
    }
    var id = $(this).attr('id').split('_')[2];
    var model = $(this).attr('model');
    $.post('/admin/'+model +'/delete', { 'id':id }, function(json){
      if (json) {
        json.error_code = parseInt(json.error_code);
        if (json.error_code == 0) {
          alertMsg('删除成功');
          window.location.reload();
        } else {
          alertMsg(json.error_desc);
        }
      } else {
        alert('未知错误,请刷新页面后重试');
      }
    },'json');
  });

  // 自动完成
    if ($.isFunction($.fn.typeahead))
     {
        /**
         * 更多使用方法参考：
         * https://github.com/twitter/typeahead.js
         */
        $(".typeahead").each(function(i, el)
        {
            function extractor(query) {
                console.log(query);
                var result = /([^,]+)$/.exec(query);
                if(result && result[1])
                    return result[1].trim();
                return '';
            }

            var $this = $(el),
            opts = {
                name: $this.attr('name') ? $this.attr('name') : ($this.attr('id') ? $this.attr('id') : 'tt'),
                displayKey: $this.attr('displayKey') ? $this.attr('displayKey') : 'label',
            },
            valueKey = $this.attr('valueKey') ? $this.attr('valueKey') : 'id';
            if ($this.hasClass('tagsinput')) return;

            //是否把值放入其它目录
            var $target = $($this.data('target'));

            var Bdopt = {
                      name: $this.attr('name') ? $this.attr('name') : ($this.attr('id') ? $this.attr('id') : 'tt'),
                      datumTokenizer: Bloodhound.tokenizers.obj.whitespace(valueKey),  
                      queryTokenizer: Bloodhound.tokenizers.whitespace
                    }

            if ($this.data('local'))
            {
                var local = $this.data('local');
                local = local.replace(/\s*,\s*/g, ',').split(',');
                Bdopt['local'] = local;
            }
            if ($this.data('prefetch'))
            {
                var prefetch = $this.data('prefetch');
                Bdopt['prefetch'] = prefetch;
            }
            if ($this.data('remote'))
            {
                var remote = $this.data('remote');
                Bdopt['remote'] = {
                  url:remote,
                  replace:function(query){
                        var keyword = $this.val().split(',').pop();
                        return query.replace('%QUERY', keyword);
                      },
                  filter: function(response){
                    if (!$target || !$target.val().length) {
                      return response;
                    };

                    var tmp = [];

                    response.forEach(function(item){
                      if (!in_array(item[valueKey], $target.val().split(','))) {
                        tmp.push(item);
                      };
                    });

                    return tmp;
                  }};
                
            }
            
            opts['templates'] = {
              suggestion: null,
              empty: function(){return '<p style="padding:0 10px;">'+($this.data('emptyString') || '未找到相关数据') +'</p>';}
            };

            if ( window[$this.data('view-callback')] && (typeof window[$this.data('view-callback')] == 'function')) {
              opts.templates['suggestion'] = window[$this.data('view-callback')];
            };

            var Bd = new Bloodhound(Bdopt);
            Bd.initialize();
            opts['source'] = Bd.ttAdapter();

            $this.typeahead(null, opts).on('typeahead:selected', function(e, data) {
                                    //用于多选时把值塞到对象文本框，且逗号隔开
                                    if ($target) {
                                      var tvalue = '';
                                      if ($target.val().length) {
                                        tvalue = $target.val().split(' , ');
                                        if (!in_array(data[valueKey], tvalue)) {
                                          tvalue.push(data[valueKey]);
                                        };
                                        
                                      } else {
                                        tvalue = [data[valueKey]];
                                      }
                                      $target.val(tvalue.join(' , '));
                                      $this.val('');
                                    };
                                  });
        });
    }

    //ajax修改排序
    $('.sorter').change(function(){
      var _this = $(this);
      var ajaxChangeSortUrl = ajaxChangeSortUrl || window.location.origin + location.pathname.replace(/\/\w+$/, '/update-sort');
      var activityId        = _this.data('id');
      var old               = _this.data('old');
      var sort              = _this.val();
      if (isNaN(sort) || sort < 0) {
        return _this.val(0);
      } else if(sort > 100000) {
         _this.val(100000);
         sort = 100000;
      }
      loading(true);
      if (sort != old) {
        $.get(ajaxChangeSortUrl + '/' + activityId + '/' + sort, function(response){
          loading(false);
          if (response.status) {
            alertMsg('操作成功！');
            _this.data('old', response.data.sort);
            _this.val(response.data.sort);
          } else {
            alertMsg('操作失败！', 'danger');
          }
        });
      };
    });

})

function in_array (item, array) {
  flag = false;
  array.forEach(function(itt){
    if (itt == item) {
      return flag = true;
    };
  });

  return flag;
}