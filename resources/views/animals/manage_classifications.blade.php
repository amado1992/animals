@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Manage classifications</h1>
                <div class="row mb-3">
                    <div class="col-md-6">
                        {!! Form::label('class_id', 'Class') !!}
                        {!! Form::select('manage_class', $classes, null, ['id' => 'manage_class', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                    <div class="col-md-6 align-self-end">
                        @if (Auth::user()->hasPermission('classifications.create'))
                            <a href="#" id="addNewClass" class="btn btn-light"><i class="fas fa-plus"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.update'))
                            <a href="#" id="editClass" class="btn btn-light"><i class="fas fa-edit"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.delete'))
                            <a href="#" id="deleteClass" class="btn btn-light"><i class="fas fa-window-close"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        {!! Form::label('order_id', 'Order') !!}
                        {!! Form::select('manage_order', array(), null, ['id' => 'manage_order', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                        {!! Form::text('new_order', null, ['id' => 'new_order', 'orderId' => 0, 'class' => 'form-control d-none']) !!}
                    </div>
                    <div class="col-md-6 align-self-end">
                        @if (Auth::user()->hasPermission('classifications.create'))
                            <a href="#" id="addNewOrder" class="btn btn-light disabled"><i class="fas fa-plus"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.update'))
                            <a href="#" id="editOrder" class="btn btn-light disabled"><i class="fas fa-edit"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.delete'))
                            <a href="#" id="deleteOrder" class="btn btn-light disabled"><i class="fas fa-window-close"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        {!! Form::label('family_id', 'Family') !!}
                        {!! Form::select('manage_family', array(), null, ['id' => 'manage_family', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                        {!! Form::text('new_family', null, ['id' => 'new_family', 'familyId' => 0, 'class' => 'form-control d-none']) !!}
                    </div>
                    <div class="col-md-6 align-self-end">
                        @if (Auth::user()->hasPermission('classifications.create'))
                            <a href="#" id="addNewFamily" class="btn btn-light disabled"><i class="fas fa-plus"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.update'))
                            <a href="#" id="editFamily" class="btn btn-light disabled"><i class="fas fa-edit"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.delete'))
                            <a href="#" id="deleteFamily" class="btn btn-light disabled"><i class="fas fa-window-close"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        {!! Form::label('genus_id', 'Genus') !!}
                        {!! Form::select('manage_genus', array(), null, ['id' => 'manage_genus', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                        {!! Form::text('new_genus', null, ['id' => 'new_genus', 'genusId' => 0, 'class' => 'form-control d-none']) !!}
                    </div>
                    <div class="col-md-6 align-self-end">
                        @if (Auth::user()->hasPermission('classifications.create'))
                            <a href="#" id="addNewGenus" class="btn btn-light disabled"><i class="fas fa-plus"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.update'))
                            <a href="#" id="editGenus" class="btn btn-light disabled"><i class="fas fa-edit"></i></a>
                        @endif
                        @if (Auth::user()->hasPermission('classifications.delete'))
                            <a href="#" id="deleteGenus" class="btn btn-light disabled"><i class="fas fa-window-close"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="#" id="resetAllValues" class="btn btn-secondary mr-2">Reset</a>
                        <a href="{{ route('animals.index') }}" class="btn btn-primary" type="button">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('animals.add_edit_classification_modal', ['modalId' => 'addOrEditClassification'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#manage_class").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getOrdersByClass') }}",
            data:{value: value},
            success:function(data){
                $("#manage_order").empty();
                $('#manage_order').append('<option value="">- select -</option>');
                $.each(data.orders, function(key, value) {
                    $('#manage_order').append('<option value="'+ value +'">'+ key +'</option>');
                });

                if(Object.keys(data.orders).length > 0) {
                    $("#addNewOrder").removeClass('disabled');
                    $("#editOrder").removeClass('disabled');
                    $("#deleteOrder").removeClass('disabled');
                }
                else {
                    $("#addNewOrder").addClass('disabled');
                    $("#editOrder").addClass('disabled');
                    $("#deleteOrder").addClass('disabled');
                }
            }
        });
    });

    $("#manage_order").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getFamiliesByOrder') }}",
            data:{value: value},
            success:function(data){
                $("#manage_family").empty();
                $('#manage_family').append('<option value="">- select -</option>');
                $.each(data.families, function(key, value) {
                    $('#manage_family').append('<option value="'+ value +'">'+ key +'</option>');
                });

                if(Object.keys(data.families).length > 0) {
                    $("#addNewFamily").removeClass('disabled');
                    $("#editFamily").removeClass('disabled');
                    $("#deleteFamily").removeClass('disabled');
                }
                else {
                    $("#addNewFamily").addClass('disabled');
                    $("#editFamily").addClass('disabled');
                    $("#deleteFamily").addClass('disabled');
                }
            }
        });
    });

    $("#manage_family").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getGenusByFamily') }}",
            data:{value: value},
            success:function(data){
                $("#manage_genus").empty();
                $('#manage_genus').append('<option value="">- select -</option>');
                $.each(data.genuss, function(key, value) {
                    $('#manage_genus').append('<option value="'+ value +'">'+ key +'</option>');
                });

                if(Object.keys(data.genuss).length > 0) {
                    $("#addNewGenus").removeClass('disabled');
                    $("#editGenus").removeClass('disabled');
                    $("#deleteGenus").removeClass('disabled');
                }
                else {
                    $("#addNewGenus").addClass('disabled');
                    $("#editGenus").addClass('disabled');
                    $("#deleteGenus").addClass('disabled');
                }
            }
        });
    });

    $("#addNewClass").click(function() {
        $('#addOrEditClassification [name=classification_rank]').val('class');
        $('#addOrEditClassification').modal('show');
    });

    $("#editClass").click(function() {
        if($("#manage_class").val() != '') {
            var id = $('#manage_class').val();

            $.ajax({
                type:'POST',
                url:"{{ route('classifications.getClassificationById') }}",
                data:{
                    id: id
                },
                success:function(data){
                    $("#addOrEditClassification [name=classification_id]").val(id);
                    $("#addOrEditClassification [name=classification_common_name]").val(data.classification.common_name);
                    $("#addOrEditClassification [name=classification_common_name_spanish]").val(data.classification.common_name_spanish);
                    $("#addOrEditClassification [name=classification_scientific_name]").val(data.classification.scientific_name);
                    $('#addOrEditClassification [name=classification_code]').val(data.classification.code);
                    $('#addOrEditClassification [name=classification_rank]').val(data.classification.rank);
                    $('#addOrEditClassification').modal('show');
                }
            });
        }
    });

    $("#addNewOrder").click(function() {
        var classId = $('#manage_class').val();

        $('#addOrEditClassification [name=classification_rank]').val('order');
        $('#addOrEditClassification [name=classification_belongs_to]').val(classId);
        $('#addOrEditClassification').modal('show');
    });

    $("#editOrder").click(function() {
        if($("#manage_order").val() != '') {
            var id = $('#manage_order').val();

            $.ajax({
                type:'POST',
                url:"{{ route('classifications.getClassificationById') }}",
                data:{
                    id: id
                },
                success:function(data){
                    $("#addOrEditClassification [name=classification_id]").val(id);
                    $("#addOrEditClassification [name=classification_common_name]").val(data.classification.common_name);
                    $("#addOrEditClassification [name=classification_common_name_spanish]").val(data.classification.common_name_spanish);
                    $("#addOrEditClassification [name=classification_scientific_name]").val(data.classification.scientific_name);
                    $('#addOrEditClassification [name=classification_code]').val(data.classification.code);
                    $('#addOrEditClassification [name=classification_rank]').val(data.classification.rank);
                    $('#addOrEditClassification').modal('show');
                }
            });
        }
    });

    $("#addNewFamily").click(function() {
        var orderId = $('#manage_order').val();

        $('#addOrEditClassification [name=classification_rank]').val('family');
        $('#addOrEditClassification [name=classification_belongs_to]').val(orderId);
        $('#addOrEditClassification').modal('show');
    });

    $("#editFamily").click(function() {
        if($("#manage_family").val() != '') {
            var id = $('#manage_family').val();

            $.ajax({
                type:'POST',
                url:"{{ route('classifications.getClassificationById') }}",
                data:{
                    id: id
                },
                success:function(data){
                    $("#addOrEditClassification [name=classification_id]").val(id);
                    $("#addOrEditClassification [name=classification_common_name]").val(data.classification.common_name);
                    $("#addOrEditClassification [name=classification_common_name_spanish]").val(data.classification.common_name_spanish);
                    $("#addOrEditClassification [name=classification_scientific_name]").val(data.classification.scientific_name);
                    $('#addOrEditClassification [name=classification_code]').val(data.classification.code);
                    $('#addOrEditClassification [name=classification_rank]').val(data.classification.rank);
                    $('#addOrEditClassification').modal('show');
                }
            });
        }
    });

    $("#addNewGenus").click(function() {
        var familyId = $('#manage_family').val();

        $('#addOrEditClassification [name=classification_rank]').val('genus');
        $('#addOrEditClassification [name=classification_belongs_to]').val(familyId);
        $('#addOrEditClassification').modal('show');
    });

    $("#editGenus").click(function() {
        if($("#manage_genus").val() != '') {
            var id = $('#manage_genus').val();

            $.ajax({
                type:'POST',
                url:"{{ route('classifications.getClassificationById') }}",
                data:{
                    id: id
                },
                success:function(data){
                    $("#addOrEditClassification [name=classification_id]").val(id);
                    $("#addOrEditClassification [name=classification_common_name]").val(data.classification.common_name);
                    $("#addOrEditClassification [name=classification_common_name_spanish]").val(data.classification.common_name_spanish);
                    $("#addOrEditClassification [name=classification_scientific_name]").val(data.classification.scientific_name);
                    $('#addOrEditClassification [name=classification_code]').val(data.classification.code);
                    $('#addOrEditClassification [name=classification_rank]').val(data.classification.rank);
                    $('#addOrEditClassification').modal('show');
                }
            });
        }
    });

    $("#resetAllValues").click(function() {
        $("#manage_class").val('');

        $("#manage_order").val('');
        $("#addNewOrder").addClass('disabled');
        $("#editOrder").addClass('disabled');
        $("#deleteOrder").addClass('disabled');

        $("#manage_family").val('');
        $("#addNewFamily").addClass('disabled');
        $("#editFamily").addClass('disabled');
        $("#deleteFamily").addClass('disabled');

        $("#manage_genus").val('');
        $("#addNewGenus").addClass('disabled');
        $("#editGenus").addClass('disabled');
        $("#deleteGenus").addClass('disabled');
    });

    $('#addOrEditClassification').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $("#addOrEditClassification").on('submit', function (event) {
        event.preventDefault();

        var classificationId = $("#addOrEditClassification [name=classification_id]").val();
        var commonName = $("#addOrEditClassification [name=classification_common_name]").val();
        var spanishCommonName = $("#addOrEditClassification [name=classification_common_name_spanish]").val();
        var scientificName = $("#addOrEditClassification [name=classification_scientific_name]").val();
        var code = $("#addOrEditClassification [name=classification_code]").val();
        var rank = $("#addOrEditClassification [name=classification_rank]").val();
        var belongsTo = $("#addOrEditClassification [name=classification_belongs_to]").val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.saveOrEditClass') }}",
            data:{
                classificationId: classificationId,
                commonName: commonName,
                spanishCommonName: spanishCommonName,
                scientificName: scientificName,
                code: code,
                rank: rank,
                belongsTo: belongsTo
            },
            success:function(data){
                if(data.success) {
                    $('#addOrEditClassification').modal('hide');

                    if(classificationId == 0) {
                        switch (rank) {
                            case 'class':
                                $('#manage_class').append('<option value="'+ data.newClassificationId +'">'+ commonName +'</option>');
                                break;
                            case 'order':
                                $('#manage_order').append('<option value="'+ data.newClassificationId +'">'+ commonName +'</option>');
                                break;
                            case 'family':
                                $('#manage_family').append('<option value="'+ data.newClassificationId +'">'+ commonName +'</option>');
                                break;
                            default:
                                $('#manage_genus').append('<option value="'+ data.newClassificationId +'">'+ commonName +'</option>');
                                break;
                        }
                    }
                    else {
                        switch (rank) {
                            case 'class':
                                $("#manage_class option[value="+classificationId+"]").html(commonName);
                                break;
                            case 'order':
                                $("#manage_order option[value="+classificationId+"]").html(commonName);
                                break;
                            case 'family':
                                $("#manage_family option[value="+classificationId+"]").html(commonName);
                                break;
                            default:
                                $("#manage_genus option[value="+classificationId+"]").html(commonName);
                                break;
                        }
                    }
                }
                else
                    alert(data.message);
            }
        });
    });

</script>

@endsection
