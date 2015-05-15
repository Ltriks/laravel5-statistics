<?php if ($object->lastPage() > 1): ?>
    
            <?php $object->setPath(preg_replace('/&page=\d*/', '', $_SERVER['REQUEST_URI'])) ?>
            <?php  echo $object->render(); ?>
    <ul class="pagination" style="float:right;">
        <span>
            <input type="text" value="<?php echo Request::Input('page') ?>" id="gotopage" class="gotopage form-control" onchange="if(isNaN(parseInt(this.value))) {this.value = '';} else {this.value=parseInt(this.value);};"  placeholder="" style="width: 100px; display: inline;margin: 0 5px;">
            <button id="_goto" type="button" class="btn btn-white" onclick="var n=parseInt(document.getElementById('gotopage').value); if(isNaN(n)|| n <=0 || n > <?php echo $object->lastPage();?>) return alert('页码不存在！'); var url=window.location.href; if(url.indexOf('?') < 0) url += '?'; if(url.indexOf('page') < 0) url+= '&page=1'; url = url.replace(/page=\d*/, 'page=' + n);window.location.href=url">GO</button>
        </span>
        <span>
            <button type="button" class="btn btn-default">共<?php echo $object->total(); ?>条记录</button>
        </span>
    </ul>
<?php endif; ?>