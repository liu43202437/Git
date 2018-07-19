<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="renderer" content="webkit">
<title></title>
<link rel="stylesheet" href="public/css/pintuer.css">
<link rel="public/stylesheet" href="css/admin.css">
<script src="public/js/jquery.js"></script>
<script src="public/js/pintuer.js"></script>
</head>
<body>
<form method="post" action="" id="listform">
  <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 内容列表</strong> <a href="" style="float:right; display:none;">添加字段</a></div>
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:center; padding-left:20px;">ID</th>
        <th width="20%" style="text-align:center; padding-left:20px;">标题</th>
        <th width="35%" style="text-align:center; padding-left:20px;">内容</th>
        <th style="text-align:center; padding-left:20px;" >操作</th>
      </tr>
      <?php 
		foreach($datas as $v){
	  ?>
      <tr>
	  <td><input type="checkbox" name="id[]" value="<?php echo $v['id'];?>" /><?php echo $v['id'] ;?></td>
      <td width="20%" ><?php echo $v['title'] ;?></td>
      <td width="35%" ><?php echo $v['content'];?></td>
      <td><div class="button-group"> <a class="button border-main" href="index.php?c=demo&a=update&id=<?php echo $v['id']; ?>"><span class="icon-edit"></span> 修改</a> <a id="dellink" class="button border-red dellink" href="index.php?c=demo&a=dodel&id=<?php echo $v['id']; ?>"><span class="icon-trash-o"></span> 删除</a></div></td>
      </tr>
		<?php }?>
      <tr>
        <td style="text-align:left; padding:19px 0;padding-left:40px;"><input type="checkbox" id="checkall"/>
          全选 </td>
        <td colspan="7" style="text-align:left;padding-left:20px;"><a href="javascript:void(0)" class="button border-red icon-trash-o" style="padding:5px 15px;" onclick="DelSelect()"> 删除</a> <a href="javascript:void(0)" style="padding:5px 15px; margin:0 10px;" class="button border-blue icon-edit" onclick="sorts()"> 排序</a>
        </td>
      </tr>
    </table>
  </div>
</form>
<script type="text/javascript">
$(function(){
        $('.dellink').click(function(){
            if(confirm("确认删除?")){
                $('.dellink').fadeOut(500);
            }else{
                return false;
            }
        })
        $('#zhuan').click(function(){
            var page=$('#tiao').val();
            var key=$('#keywords').val();
            var cate=$('#cate').val();
            var zd=$('#zd').val();
            var sy=$('#sy').val();
            var tj=$('#tj').val();
          window.location.href='list.php?page='+page+'&keywords='+key+'&cateid='+cate+'&zd='+zd+'&sy='+sy+'&tj='+tj;
        })
        $('#search').click(function(){
          var key=$('#keywords').val();
          var cate=$('#cate').val();
          var zd=$('#zd').val();
          var sy=$('#sy').val();
          var tj=$('#tj').val();
          window.location.href='list.php?keywords='+key+'&cateid='+cate+'&zd='+zd+'&sy='+sy+'&tj='+tj;
        })
    }) 

//搜索
function changesearch(){	
		
}

//单个删除
function del(id,mid,iscid){
	if(confirm("您确定要删除吗?")){
		
	}
}

//全选
$("#checkall").click(function(){ 
  $("input[name='id[]']").each(function(){
	  if (this.checked) {
		  this.checked = false;
	  }
	  else {
		  this.checked = true;
	  }
  });
})

//批量删除
function DelSelect(){
  var Checkbox=false;
   $("input[name='id[]']").each(function(){
    if (this.checked==true) {   
    Checkbox=true;  
    }
  });
  if (Checkbox){
    var t=confirm("您确认要删除选中的内容吗？");
    if (t==false) return false;
    $("#listform").attr("action", "doDels.php");   //给listform的action赋值
    $("#listform").submit();    //提交表单
  }
  else{
    alert("请选择您要删除的内容!");
    return false;
  }
}

//批量排序
function sorts(){
  var Checkbox=false;
   $("input[name='id[]']").each(function(){
    if (this.checked==true) {   
    Checkbox=true;  
    }
  });
  if (Checkbox){  
    $("#listform").attr("action", "dosort.php");    //给listform的action赋值
    $("#listform").submit();    
  }
  else{
    alert("请选择要操作的内容!");
    return false;
  }
}


//批量首页显示
function changeishome(o){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){
		
		$("#listform").submit();	
	}
	else{
		alert("请选择要操作的内容!");		
	
		return false;
	}
}

//批量推荐
function changeisvouch(o){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){
		
		
		$("#listform").submit();	
	}
	else{
		alert("请选择要操作的内容!");	
		
		return false;
	}
}

//批量置顶
function changeistop(o){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){		
		
		$("#listform").submit();	
	}
	else{
		alert("请选择要操作的内容!");		
	
		return false;
	}
}


//批量移动
function changecate(o){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){		
		
		$("#listform").submit();		
	}
	else{
		alert("请选择要操作的内容!");
		
		return false;
	}
}

//批量复制
function changecopy(o){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){	
		var i = 0;
	    $("input[name='id[]']").each(function(){
	  		if (this.checked==true) {
				i++;
			}		
	    });
		if(i>1){ 
	    	alert("只能选择一条信息!");
			$(o).find("option:first").prop("selected","selected");
		}else{
		
			$("#listform").submit();		
		}	
	}
	else{
		alert("请选择要复制的内容!");
		$(o).find("option:first").prop("selected","selected");
		return false;
	}
}

</script>
</body>
</html>