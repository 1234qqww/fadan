<link rel="stylesheet" href="{{asset('easyweb/assets/libs/layui/css/layui.css')}}"/>
<link rel="stylesheet" href="{{asset('easyweb/assets/module/admin.css?v=304')}}"/>
<script type="text/javascript" src="{{asset('easyweb/assets/libs/jquery/jquery-3.2.1.min.js')}}"></script>
<script type="text/javascript" src="{{asset('easyweb/assets/libs/layui/layui.js')}}"></script>
<script type="text/javascript" src="{{asset('easyweb/assets/js/common.js?v=304')}}"></script>
<script type="text/javascript" src="{{asset('js/wangEditor.min.js')}}"></script>
<script type="text/javascript" charset="utf-8" src="{{asset('uEditor/ueditor.config.js')}}"> </script>
<script type="text/javascript" charset="utf-8" src="{{asset('uEditor/ueditor.all.min.js')}}"> </script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="{{asset('uEditor/lang/zh-cn/zh-cn.js')}}"></script>

<form id="modelUserForm" lay-filter="modelUserForm" class="layui-form model-form">
    <input name="userId" type="hidden"/>
    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block">
            <input name="title" id="title" placeholder="请输入标题" type="text" class="layui-input" maxlength="20"
                   lay-verType="tips" lay-verify="required" value="{{$info['title']}}" required/>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">内容</label>
        <div class="layui-input-block">
            <script id="editor" type="text/plain" style="width:100%;height:500px;margin: 0 auto"></script>
        </div>
        <script id="editor" type="text/plain" style="width:100%;height:300px;margin: 0 auto"></script>
        <input type="hidden" id="content" name="content" value="{{$info['content']}}">
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">配图</label>
        <div class="layui-input-block">
            <button type="button" class="layui-btn" id="test1" style="margin-bottom:20px"><i class="layui-icon"></i>配图</button>
            <blockquote class="layui-elem-quote layui-quote-nm" style="width:700px" style="margin-top: 10px;">
                预览图：
                <div class="layui-upload-list" id="demo1">
                    @if($info['images'])
                        @foreach($info['images'] as $key=>$value)
                            <img style="width: 120px; height: 120px" data-url="{{$value}}" src="{{$value}}" alt="" class="layui-upload-img images image">
                            <img class="del-image" style="cursor:pointer;position: relative;left: -35px;top: -40px;width: 25px; height: 25px;" src="{{asset('icon/delete.png')}}">
                        @endforeach
                    @endif
                </div>
            </blockquote>
            <div style="margin:20px;color:#c0c0c0">建议图片大小为：360*140</div>
            <input type="hidden" id="image" name="image" lay-verify="title" autocomplete="off" class="layui-input" value="{{$info['image']}}">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">阅读量</label>
        <div class="layui-input-block">
            <input name="reading" id="reading" placeholder="请输入阅读量" type="text" class="layui-input"
                   lay-verType="tips" lay-verify="required" value="{{$info['reading']}}" required/>
        </div>
        <div id="getContent"  onclick="getContent()" hidden></div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input name="sort" id="sort" placeholder="请输入位置" type="text" class="layui-input"
                   lay-verType="tips" lay-verify="required" value="{{$info['sort']}}" required/>
        </div>
    </div>
</form>
<script>
    var ue = UE.getEditor('editor');
    ue.ready(function() {//编辑器初始化完成再赋值
        ue.setContent( $('#content').val());  //赋值给UEditor
    });
    function getContent() {
        $('#content').val( UE.getEditor('editor').getContent())
    }
    layui.use(['layer', 'form', 'table', 'util', 'admin', 'formSelects'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var util = layui.util;
        var admin = layui.admin;
        var formSelects = layui.formSelects;

    });

    // //第一步是先获取服务器传过来的图文信息值
    var info1 = document.getElementById("content").value;
    // //把图文信息的值通过innerHTML赋值给编辑器
    document.getElementById("editor").innerHTML=info1;

    var E = window.wangEditor
    var editor = new E('#editor')
    // 或者 var editor = new E( document.getElementById('editor') )
    // 隐藏“网络图片”tab
    editor.customConfig.showLinkImg = false;
    editor.customConfig.uploadFileName = 'file';
    editor.customConfig.uploadImgMaxLength = 5;
    editor.customConfig.uploadImgServer = ""  // 上传图片到服务器
    editor.customConfig.uploadImgServer = "{{url('editUpload')}}"  // 上传图片到服务器

    editor.create()
    document.getElementById('btn1').addEventListener('click', function () {
        // 读取 html
        console.log(editor.txt.html());
        var html = editor.txt.html();
        $('#content').val(html);
    })
    var incon="{{asset('icon/delete.png')}}"
    $('body').on('click', '.del-image', function(){
        let par = $(this).prev()
        par.remove()
        $(this).remove();
    })
    //多图上传
    layui.use('upload', function(){
        var $ = layui.jquery
            ,upload = layui.upload;
        // 配图
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: "{{url('uploads')}}"
            , multiple: true
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                });
            }
            ,done: function(res){
                $('#demo1').append('<img style="width: 120px; height: 120px" data-url="'+res.url+'" src="' + res.url + '" alt="" class="layui-upload-img images image"><img class="del-image" style="cursor:pointer;position: relative;left: -35px;top: -40px;width: 25px; height: 25px;" src='+ incon +'>')
                var image = [];
                $('form').find('.image').each(function(){
                    image.push($(this).attr('data-url'))
                });
                $("input[name='image']").val(image);
            }
        });
    })
</script>
