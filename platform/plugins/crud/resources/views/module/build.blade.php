 {!! Form::open(array('url'=>'admin/cruds/dobuild/'.$module_name, 'class'=>'form-horizontal sximo-form','id'=>'rebuildForm')) !!}
    <div class="text-center result"></div>
    <p class="text-center" style="font-weight: bold;">
       
        <span class="text-center"> <i class="icon-arrow-down3"></i> </span>
    </p>
  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4">  </label>
    <div class="col-md-8">
    	 <h6> Build All Codes</h6>  <br />
      <a href="{{ url('admin/cruds/rebuild/'.$id)}}" class="btn btn-sm btn-success" id="rebuild" ><i class="fa fa-refresh"></i> Rebuild All Codes </a> 
      </div>
  </div> 
<hr />  

 {!! Form::close() !!}
 
<!-- <div class="modal" id="sximo-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog  " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">New message</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="sximo-modal-content">
                
            </div>
           
        </div>
    </div>
</div>-->
 <script type="text/javascript">
	$(function(){

        $('#rebuild').click(function () {
            var url = $(this).attr("href");
            $(this).html('<i class="icon-spinner7"></i> Processing .... ');
            $.get(url, function( data ) {
            	if(!data.error)
				{
					$( ".result" ).html( '<p class="alert alert-success">'+data.message+'</p>' );
             		$('#rebuild').html('<i class="icon-spinner7"></i>  Rebuild All Codes ');
					 $('#sximo-modal').modal('toggle');
				} else {
					$( ".result" ).html( '<p class="alert alert-danger">'+data.message+'</p>' );
             		$('#rebuild').html('<i class="icon-spinner7"></i>  Rebuild All Codes ');
				}
              
            });
            return false;
        })
        /*
		$('input[type="checkbox"],input[type="radio"]').iCheck({
			checkboxClass: 'icheckbox_square-red',
			radioClass: 'iradio_square-red',
		});	
		*/

		$('#rebuildForm').submit(function(){
			var act = $(this).attr('action');
			 $('#submitRbld').html('<i class="icon-spinner7"></i> Processing .... ');
			$.post(act,$(this).serialize(),
			    function(data){
			    	if(data.status=='success')
			    	{
			    		$.get(data.url, function( json ) {
				            $( ".result" ).html( '<p class="alert alert-success">'+json.message+'</p>' );
				            $('#submitRbld').html('<i class="icon-spinner7"></i>  Rebuild Now ');
				             //alert(json.message)
				        });	
			    	}
			      
			    }, "json");
			return false;
		});
		
	})
 </script>

