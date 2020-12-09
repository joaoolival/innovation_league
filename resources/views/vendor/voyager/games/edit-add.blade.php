@extends('voyager::bread.edit-add')
@section('javascript')
    @parent
<script>

//form-control select2-ajax select2-hidden-accessible

$('document').ready(function () {
    var id_group = 0;
    var id_group_old = 0;
    var id_type = 0;
    
    $('select.select2-ajax').select2('destroy');
    $('select.select2-ajax').each(function() {
        $(this).select2({
            width: '100%',
            ajax: {
                url: $(this).data('get-items-route'),
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: $(this).data('get-items-field'),
                        method: $(this).data('method'),
                        id: $(this).data('id'),
                        page: params.page || 1,
                        group: id_group,
                        type_game: id_type
                    }
                    return query;
                }
            }
        });

        $(this).on('select2:select',function(e){
            var data = e.params.data;
            if (data.id == '') {
                // "None" was selected. Clear all selected options
                $(this).val([]).trigger('change');
                if(e.currentTarget.name == 'id_group'){
                    id_group = 0;
                }
                if(e.currentTarget.name == 'id_type'){
                    id_type = 0;
                    $(this).parent().next().show( "slow" );
                }
            } else {
                $(e.currentTarget).find("option[value='" + data.id + "']").attr('selected','selected');
                if(e.currentTarget.name == 'id_group'){
                    id_group = e.currentTarget.value;
                }
                if(e.currentTarget.name == 'id_type'){
                    id_type = e.currentTarget.value;
                    if(e.currentTarget.value > 1){
                        $(this).parent().next().hide( "slow" );
                        id_group_old = id_group;
                        id_group = 0;
                    }else{
                        $(this).parent().next().show( "slow" );
                        id_group = id_group_old;
                    }
                }
                
            }
        });

        $(this).on('select2:unselect',function(e){
            var data = e.params.data;
            $(e.currentTarget).find("option[value='" + data.id + "']").attr('selected',false);
        });
    });
     
});

</script>
@endsection

