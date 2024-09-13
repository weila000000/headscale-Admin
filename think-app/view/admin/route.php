<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>layui table 组件综合演示</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/res/layui/css/layui.css" rel="stylesheet">
  <link href="/res/adminui/dist/css/admin.css" rel="stylesheet">
</head>
<body>


  
  <div class="layui-fluid">
    <div class="layui-row layui-col-space15">
      <div class="layui-col-md12">
        <div class="layui-card">
          <div class="layui-card-header">路由中心</div>
          <div class="layui-card-body">
            <table class="layui-hide" id="test-table-index" lay-filter="test-table-index"></table>

            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="reload">刷新</button>
              </div>
            </script>
             


            <script type="text/html" id="test-table-switchTpl">
              <!-- 这里的 checked 的状态只是演示 -->
              <input type="checkbox" name="启用" lay-skin="switch" lay-text="是|否" lay-filter="test-table-sexDemo"
               value="{{ d.enable }}" data-json="{{ encodeURIComponent(JSON.stringify(d)) }}" {{ d.enable == "1" ? 'checked' : '' }}>
               &nbsp;
               <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
            </script>
             

          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="/res/layui/layui.js"></script>  
  <script>
  layui.config({
    base: '/res/' // 静态资源所在路径
  }).use(['index', 'table', 'dropdown','form'], function(){
    var table = layui.table
    ,form = layui.form
    ,$ = layui.$;
    var dropdown = layui.dropdown;
    
    // 创建渲染实例
    table.render({
      elem: '#test-table-index'
      ,url: '/api/getRoute' // 此处为静态模拟数据，实际使用时需换成真实接口
      ,toolbar: '#toolbarDemo'
      ,defaultToolbar: ['filter', 'exports', 'print', {
        title: '帮助'
        ,layEvent: 'LAYTABLE_TIPS'
        ,icon: 'layui-icon-tips'
      }]
      ,height: 'full-100' // 最大高度减去其他容器已占有的高度差
      ,cellMinWidth: 80
      ,totalRow: true // 开启合计行
      ,page: true
      ,cols: <?php echo htmlspecialchars_decode($cols_data);?>
      ,error: function(res, msg){
        console.log(res, msg)
      }
    });


    //事件-开关操作
    form.on('switch(test-table-sexDemo)', function(obj){
      var $ = layui.$;
      var json = JSON.parse(decodeURIComponent($(this).data('json')));
      //layer.tips(this.value + ' ' + this.name + '：'+ obj.elem.checked, obj.othis);
      
      json = table.clearCacheKey(json);
      console.log(obj.elem.checked); 

      if (obj.elem.checked){
        console.log("执行打开路由");
        $.ajax({
              url:'/api/route_enable',
              type: 'post',
              dataType: 'json',
              data: {route_id: json.id},
              success:function(res){
                  console.log(res)
                  if(res.code == 0){
                    layer.msg('打开成功', {icon: 6});
                  }else{
                    layer.msg(res.msg, {icon: 7});
                  }
              }
        });
      }else{
        console.log("执行关闭路由")
        $.ajax({
              url:'/api/route_disable',
              type: 'post',
              dataType: 'json',
              data: {route_id: json.id},
              success:function(res){
                  console.log(res)
                  if(res.code == 0){
                    layer.msg('关闭成功', {icon: 6});
                  }else{
                    layer.msg('关闭失败', {icon: 7});
                  }
              }
        });
      }

      
    });
    
    // 工具栏事件
    table.on('toolbar(test-table-index)', function(obj){
      var $ = layui.$;
      var util = layui.util;
      var id = obj.config.id;
      var checkStatus = table.checkStatus(id);
      var othis = lay(this);
      switch(obj.event){
        case 'reload':
          var data = checkStatus.data;
          table.reload('test-table-index',true);
        break;

      
      };
    });
   
    //触发单元格工具事件
    table.on('tool(test-table-index)', function(obj){ // 双击 toolDouble
      var $ = layui.$;
      var data = obj.data;
      // console.log(obj)
      if(obj.event === 'del'){
        layer.confirm('真的删除行么', function(index){
          obj.del();
          layer.close(index);

          $.ajax({
            url:'/api/delRoute',
            type: 'post',
            dataType: 'json',
            data: {route_id: obj.data.id},
            success:function(res){
              if(res.code == 0){
                layer.msg('删除成功', {icon: 6});
              }else{
                layer.msg('删除失败', {icon: 7});
              }
              
            }
          });
          
        });
      }
    });


   

  });
  </script>
</html>