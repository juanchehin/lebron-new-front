var Table = new Object(null);
$(function ()
{
    const attr_activo = "activo", attr_portada = "portada";
    var classname = '!' + document.querySelector('base').getAttribute('data-class');
    var nestable = $('#nestable'), count_check_portada = nestable.data('portada');
    var modal = "";
    modal += '<div id="modal-item" class="modal fade" role="dialog">';
    modal += '<div class="modal-dialog">';
    modal += '<div class="modal-content">';
    modal += '<div class="modal-header">';
    modal += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
    modal += '</div>';
    modal += '<div class="modal-body"></div>';
    modal += '</div>';
    modal += '</div>';
    //$('.formulario').reset();
    $('body').prepend(modal);
    control_checked();

    $('#btn-principal').click(function ()
    {
        Table.nestable_edit();
    });

    Table.get_nestable_id = function (item)
    {
        return item.closest('li').getAttribute('data-id');
    };

    Table.nestable_drop = function (item)
    {
        //_callback();
        var id = Table.get_nestable_id(item);
        jconfirm(function (res)
        {
            if (res)
            {
                $.getJSON(classname + '/eliminar', {'id': id}, function (status)
                {
                    if (status.success)
                    {
                        $('li[data-id="' + id + '"]').fadeOut(300, function ()
                        {
                            $(this).remove();
                        });
                    }
                });
            }
        }, '¿Realmente desea eliminar el &iacute;tem <b>#'+id+'</b>?');
    };

    Table.nestable_edit = function (item)
    {
        var value = item ? Table.get_nestable_id(item) : "";
        before_send();
        $.getJSON(classname + '/form', {'id': value}, function (data)
        {
            before_send();
            $('#modal-item .modal-body').html(data.content);
            $('#modal-item').modal();
        });
    };

    $('#modal-item').on('shown.bs.modal',function ()
    {
        $(this).find('input:visible:first').focus();
    });

    Table.nestable_estado = function (check)
    {
        var checks_disabled = $(check).closest('.dd3-content').find('input[name="portada"]');
        var params = {
            'id': Table.get_nestable_id(check),
            'estado': check.checked ? 1 : 0,
            'columna': check.getAttribute('name'),
            'count_portada': count_check_portada
        };

        switch (params.columna)
        {
            case attr_activo:
                if (!params.estado)
                {
                    if(checks_disabled.is(':checked'))
                    {
                        count_check_portada++;
                    }
                    checks_disabled.attr({'disabled': true, 'checked': false});
                }
                else //if(count_check_portada)
                {
                    checks_disabled.attr('disabled', false);
                }
                break;
            case attr_portada:
                if (params.estado)
                {
                    if (count_check_portada <= 0)
                    {
                        check.checked = false;
                        //$(check).attr("checked",false);
                        return;
                    }
                    count_check_portada--;
                }
                else // if(!count_check_portada)
                {
                    count_check_portada++;
                }
                break;
        }

        $.post(classname + '/setEstado', params);
    };

    function control_checked()
    {
        nestable.find('input[name='+attr_portada+']').each(function ()
        {
            if (this.checked)
            {
                count_check_portada--;
            }
        });
    }

    $.fn.FormReset = function ()
    {
        $(this).each(function ()
        {
            $(this).find('input, textarea').val('').removeAttr('checked');

            this.reset();
        });
    };

    $('#nested').change(function ()
    {
        var max_depth = nestable.data('depth');
        if ($(this).is(':checked') && nestable.find('li').length > 1)
        {
            nestable.nestable({
                'maxDepth': max_depth //niveles
            }).on('change', function ()
            {
                var serialize = window.JSON.stringify($(this).nestable('serialize'));
                $.post(classname + '/ordenar', {'output': serialize});
            }).find('.dd3-handle').addClass('dd-handle');
        }
        else
        {
            nestable.find('.dd3-handle').removeClass('dd-handle')
        }
    });
});