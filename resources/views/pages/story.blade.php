@extends('layouts.app')

@section('content')
<x-page-header pagetitle="Story" class="bg-primary"/>
<div class="wrapper wrapper-content">
<div class="animated fadeInRightBig">
<div class="row">
<div class="col-12">
<div class="ibox-title">
<h2>List of all messages</h2>
<div class="ibox-tools">
<a href="javascript:void(0)" id="btn-add-modal" class="btn btn-info">Add Story</a>
</div>
</div>
<div class="ibox-content">
<table class="table table-bordered table-striped table-hover" id="post-table">
<thead>
<tr>
<th>ID</th>
<th>TITLE</th>
<th>STORY</th>
<th>ACTION</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
</div>
<div class="ibox-footer">
</div>
</div>
</div></div>
</div>

<!-- //ADD MODALL -->
<div class="modal inmodal" id="myAddModal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
<div class="modal-dialog">
<form id="add-form">
<div class="modal-content animated bounceInRight">
<div class="modal-title">
<h3 class="m-1">Add Story</h3>
</div>
<div class="modal-body">
<div class="row">
<div class="col-12 form-group">
<input type="text" name="title" id="title" placeholder="Title here..." class="form-control">
</div>
<div class="col-12 form-group">
<textarea name="story" id="story" class="form-control" placeholder="Your story here..."></textarea>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Save</button>
</div>
</div>
</form>
</div>
</div>

<!-- //EDIT MODALL -->
<div class="modal inmodal" id="myEditModal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
<div class="modal-dialog">
<form id="edit-form">
<div class="modal-content animated bounceInRight">
<div class="modal-title">
<h3 class="m-1">Edit Story</h3>
</div>
<div class="modal-body">
<div class="row">
<div class="col-12 form-group">
<input type="text" name="title" id="title" placeholder="Title here..." class="form-control">
</div>
<div class="col-12 form-group">
<textarea name="story" id="story" class="form-control" placeholder="Your story here..."></textarea>
</div>
<input type="hidden" name="id" id="id">
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Save changes</button>
</div>
</div>
</form>
</div>
</div>
@endsection

@section('my-js')
<script>
$(document).ready(function(){
    load_data();

    //...... DELETE RECORD
    $(document).on("click", ".b-delete", function() {
        var deleteId = $(this).data("deleteid");
        if(confirm('Delete?')){
            $.ajax({
                method:"delete",
                url:"{{ url('/api/post') }}/"+deleteId,
                success:function(data){
                    load_data();
                },
                error:function(){
                }
            });
        }else{
            return false;
        }
    });

    //...... LOAD ADD POST MODAL
    $("#btn-add-modal").click(function(){
        $("#myAddModal").modal();
    });

    //...... SAVE
    $("#add-form").submit(function(e){
    e.preventDefault();
        $.ajax({
            method:"post",
            url:"{{ url('/api/post') }}",
            data:$(this).serialize(),
            success:function(data){
                load_data();
            },
            error:function(){
            }
        });
    });

    //...... SAVE CHANGES
    $("#edit-form").submit(function(e){
        e.preventDefault();
        id = $("#edit-form #id").val()
        $.ajax({
            method:"patch",
            url:"{{ url('/api/post') }}/"+id,
            data:$(this).serialize(),
            success:function(data){
                load_data();
                $("#myEditModal").modal('hide');
                alert("Update successful");
            },
            error:function(){
            }
        });
    });

    //...... LOAD RECORD TO EDIT FORM
    $(document).on("click", ".b-edit", function() {
        $("#myEditModal").modal();
        editid = $(this).data("editid");
        $.ajax({
            method:"get",
            url:"{{ url('/api/post') }}/"+editid,
            success:function(data){
                $("#edit-form #id").val(data.id);
                $("#edit-form #title").val(data.title);
                $("#edit-form #story").val(data.story);
            }
        });
    });

    //...... LOAD RECORDS TO TABLE
    function load_data(){
        $.ajax({
            method:"get",
            url:"{{ url('/api/post') }}",
            success:function(data){
                var maxLoop = data.length;
                html="";
                for(var i =0; i < maxLoop; i++){
                    html += "<tr>";
                    html += "<td>"+data[i].id+"</td>";
                    html += "<td>"+data[i].title+"</td>";
                    html += "<td>"+data[i].story+"</td>";
                    html += "<td width='100'>";
                    html += "<button class='btn btn-info b-edit' data-editid='"+data[i].id+"'><i class='fa fa-edit'></i></button>"
                    html += "<button class='btn btn-warning b-delete' data-deleteid='"+data[i].id+"'><i class='fa fa-trash'></i></button>"
                    html += "</td>";
                    html += "</tr>";
                }
                $("#post-table tbody").html(html);
            }
        });
    }
});
</script>
@endsection