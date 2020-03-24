
@push('scripts')

    <script type="text/javascript">
        $(document).ready(function() {
            {{ (isset($id) ? $id : 'dtable') }} = $("#{{ (isset($id) ? $id : 'dtable') }}").DataTable({
                "autoWidth" : false,
                "order": [[ {{ isset($order['column']) && $order['column'] ? $order['column'] : 1 }}, "{{ isset($order['direction']) ? $order['direction'] : 'asc' }}" ]],
                "pageLength": {{ (isset($pageLength) ? $pageLength : 10) }},
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "stateSave": false,
                "deferRender": true,
                "displayStart": {{ $page }},
                @if($deferLoading == true)
                "deferLoading": {{ $data['recordsTotal'] }},
                @endif
                "ajax": {
                    "url" : "{{ $route }}",
                    "type": "GET",
                    "data": { action: 'getTable', '_token' : '{{ csrf_token() }}' }
                },
                "select": {
                    "style": 'multi',
                    "selector": 'td:first-child'
                },
                "language": {
                    'processing' : '<div class="spinner"><i class="fas fa-sync fa-spin fa-3x fa-fw" style="z-index: 9999999;"></i><span class="sr-only">Loading.</span></div>',
                    "emptyTable": "No entries available"
                },
                "columns": [
                        @empty(!$columns)
                        @foreach($columns as $_index => $column)
                    { "data" : "{{ $_index }}" , "name" : "{{ $_index }}", "targets": "{{ $loop->index }}", "orderable" : {{ !isset($column['orderable']) ? 'true' : 'false' }}, "visible" : {{ !isset($column['visible']) ? 'true' : 'false' }},  "class" : "{{isset($column['visible']) && $column['visible'] == false ? 'never' : '' }} {{ $loop->iteration == 2 ? 'all ' : ''  }}{{ isset($column['isImage']) && $column['isImage'] == true ? 'image ' : '' }}{{ !isset($column['class']) ? "" : $column['class'] }}"},
                    @endforeach
                    @endempty
                ],
                "rowId": 'index',
                "buttons": [
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-info btn-sm',
                        text: '<i class="fas fa-copy"></i> Copy',
                        exportOptions: {
                            columns: [ @php echo implode(',',range(0, (array_key_exists('action', $columns) ? count($columns) - 2 : count($columns) - 1))); @endphp ] //Your Colume value those you want
                            ,
                            stripNewlines: false,
                            format : {
                                body : function (data, column, row){
                                    return data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-info btn-sm',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        exportOptions: {
                            columns: [ @php echo implode(',',range(0, (array_key_exists('action', $columns) ? count($columns) - 2 : count($columns) - 1))); @endphp ] //Your Colume value those you want
                            ,
                            stripHtml: true,
                            stripNewlines: false,
                            format : {
                                body : function (data, column, row){

                                    return data.replace( /<br\s*\/?>/ig, '\n' ).replace(/<.*?>/g, "") ;
                                }
                            }
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        className: 'btn btn-info btn-sm',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        exportOptions: {
                            columns: [ @php echo implode(',',range(0, (array_key_exists('action', $columns) ? count($columns) - 2 : count($columns) - 1))); @endphp ] //Your Colume value those you want
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-info btn-sm',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        exportOptions: {
                            columns: [ @php echo implode(',',range(0, (array_key_exists('action', $columns) ? count($columns) - 2 : count($columns) - 1))); @endphp ] //Your Colume value those you want
                            , stripHtml: true,
                            stripNewlines: false,
                            format : {
                                body : function (data, column, row){

                                    return data.replace( /<br\s*\/?>/ig, '\n' ).replace(/<.*?>/g, "") ;
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        text: '<i class="fas fa-print"></i> Print',
                        exportOptions: {
                            columns: [ @php echo implode(',',range(0, (array_key_exists('action', $columns) ? count($columns) - 2 : count($columns) - 1))); @endphp ] //Your Colume value those you want,
                            ,
                            stripNewlines: false,
                            format : {
                                body : function (data, column, row){
                                    return data;
                                }
                            }
                        }
                    },
                    {
                        className: 'btn btn-warning btn-sm reload-table',
                        text: '<i class="fas fa-sync-alt"></i> Reload table',
                        action: function () {
                            {{ (isset($id) ? $id : 'dtable') }}.ajax.reload();
                        }
                    },
                ],
                "sDom": "<'row'<'col-lg-4 d-none d-lg-block'l><'col-lg-8 text-right d-none d-lg-block'B><'col-12't r><'col-12 dt-toolbar-footer mt-4'<'row'<'col-lg-6 mb-3 mb-lg-0'i><'col-lg-6'p>>>>",
                /*"sDom": "<'row'<'col-6'l B r><'col-6'p><'col-12't><'col-12 dt-toolbar-footer mt-4'i><'row'<'col-6'i><'col-6'p>>>>",*/
                "createdRow": function( row, data, dataIndex){
                },
                "drawCallback": function (settings, json)
                {
                    $('.image-popup').magnificPopup(
                        {
                            type: 'image',
                            closeOnContentClick: true,
                            closeBtnInside: false,
                            fixedContentPos: true,
                            mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
                            image: {
                                verticalFit: true
                            },
                            zoom: {
                                enabled: true,
                                duration: 300 // don't foget to change the duration also in CSS
                            }
                        });
                }
            });

            $("#{{ (isset($id) ? $id : 'dtable') }} thead th input").bindWithDelay( 'keyup', function () {
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).parent().index()+':visible' ).search( this.value ).draw();
            }, 500);

            $("{{ (isset($id) ? $id : 'dtable') }} thead th select:not('.ajax')").bindWithDelay( 'change', function () {
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).parent().index()+':visible' ).search( this.value ).draw();
            },500);

            $(document).on( 'change', "#{{ (isset($id) ? $id : 'dtable') }} thead th select:not('.ajax')", function () {
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).closest('th').index()+':visible' ).search( this.value ).draw();
            });

            $(document).on( 'change', "#{{ (isset($id) ? $id : 'dtable') }} thead th div div select:not('.ajax')", function () {
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).closest('th').index()+':visible' ).search( this.value, $(this).closest('.input-group').find('.ajax').val() ).draw();
            });

            $(document).on( 'keyup', "#{{ (isset($id) ? $id : 'dtable') }} thead th div div input:not('.ajax')", function () {
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).closest('th').index()+':visible' ).search( this.value, $(this).closest('.input-group').find('.ajax').val() ).draw();
            });

            {{ (isset($id) ? $id : 'dtable') }}.on( 'select deselect', function (e, dt, type, indexes) {
                //var selectedRows = dtable.rows( { selected: true } ).count();

                //dtable.button( 6 ).enable( selectedRows > 0 );
            } );

            $(document).on( 'click', '#select-rows', function (e) {
                if($(this).is(':checked')){
                    {{ (isset($id) ? $id : 'dtable') }}.rows().select();
                } else {
                    {{ (isset($id) ? $id : 'dtable') }}.rows().deselect();
                }
            } );

            var dateRangeStart, dateRangeEnd;

            // Date range script - Start of the sscript
            $(".date_range").daterangepicker({
                autoUpdateInput: false,
                locale: {
                    "cancelLabel": "Clear",
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                "opens": "left",
                //"alwaysShowCalendars": true,
            });

            $(".date_range").on('apply.daterangepicker', function(ev, picker) {
                dateRangeStart = picker.startDate;
                dateRangeEnd = picker.endDate;
                $(this).val(dateRangeStart.format('DD/MM/YY') + ' to ' + dateRangeEnd.format('DD/MM/YY'));
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).closest('th').index()+':visible' ).search( this.value ).draw();
            });

            $(".date_range").on('cancel.daterangepicker', function(ev, picker) {
                dateRangeStart = dateRangeEnd = null;
                $(this).val('');
                {{ (isset($id) ? $id : 'dtable') }}.column( $(this).closest('th').index()+':visible' ).search( this.value ).draw();
            });

            $(document).on('click', ".clear_date_range", function() {
                dateRangeStart = dateRangeEnd = null;
                $(this).closest('th').find('input').val('');
                {{ (isset($id) ? $id : 'dtable') }}.column(  $(this).closest('th').index()+':visible' ).search( this.value ).draw();
            });

            $('#range').on('change', {{ (isset($id) ? $id : 'dtable') }}.draw);
            $('#range').change(); // trigger filter

            // Hide filters if corresponding column is hidden
            function hideFilters() {
                $('.dataTable tr.filters th').each(function(){
                    var colID = $(this).data('th');
                    var col = $('.dataTable tr.header th#' + colID);

                    if((col).is(':hidden'))
                        $(this).hide();
                    else
                        $(this).show();
                });
            }

            hideFilters();

            $(window).resize(function()
            {
                hideFilters();
            });
            //End of the datable*/
        });
    </script>

@endpush

<table id="{{ (isset($id) ? $id : 'dtable') }}" class="dt-responsive nowrap {{ (isset($class) ? $class : 'table table-striped table-bordered') }} dataTable no-footer dtr-inline" style="width:100%">
    <thead>
    @empty(!$columns)
        <tr class="filters">
            @foreach($columns as $_index => $column)
                @if(isset($column['filter']) && $column['filter'] != false)
                    <th data-th="th-{{ $_index }}">
                        @if(!isset($column['filter']['type']))
                            <input type="text" class="form-control" placeholder="Search..." />
                        @else
                            @switch($column['filter']['type'])
                                @case('text')
                                <input type="text" class="form-control" placeholder="Search..." value="{{ session()->get('datatable_'.url()->current())['columns'][$loop->index]['search']['value'] ?? '' }}"/>
                                @break

                                @case('date')
                                <div class="input-group">
                                    <input type="text" class="form-control date_range" id="reportrange" placeholder="Sort by date" value="{{ session()->get('datatable_'.url()->current())['columns'][$loop->index]['search']['value'] ?? '' }}" />

                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary clear_date_range" type="button"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>

                                {{--<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </div>--}}
                                @break

                                @case('number')
                                <input type="number" class="form-control" placeholder="" value="{{ session()->get('datatable_'.url()->current())['columns'][$loop->index]['search']['value'] ?? '' }}" />
                                @break

                                @case('select')
                                <select class="form-control">
                                    @if(isset($column['filter']['values']) && !empty($column['filter']['values']))
                                        <option value="" disabled selected>Select</option>

                                        @foreach($column['filter']['values'] as $key=>$val)
                                            @if(!empty($val))
                                                <option value="{{ $key }}" {{-- session()->get('datatable_'.url()->current())['columns'][$loop->index]['search']['value'] && session()->get('datatable_'.url()->current())['columns'][$loop->index]['search']['value'] == $val ? 'selected' : '' --}}>{{ $val }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @break

                                @case('multi')
                                <div class="input-group">
                                    <div style="width: 45%;">
                                        <select class="form-control ajax" data-token="{{ csrf_token() }}" data-route="{{ $column['filter']['route'] }}" style="border-right: 0 !important;" data-field="{{ $_index }}">
                                            @if(isset($column['filter']['options']) && !empty($column['filter']['options']))
                                                <option value="" disabled selected>Select</option>

                                                @foreach($column['filter']['options'] as $key=>$val)
                                                    @if(!empty($val))
                                                        <option value="{{ $val }}" >{{ $val }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="input-group-prepend" style="width: 10%;">
                                        <span class="input-group-text w-100 justify-content-center">=</span>
                                    </div>

                                    <div class="field_{{ $_index }}" style="width: 45%;">
                                        @if(is_null($column['filter']['values']))
                                            <input type="text" class="form-control" placeholder="" />
                                        @else
                                            <select class="form-control">
                                                @if(isset($column['filter']['values']) && !empty($column['filter']['values']))
                                                    @foreach($column['filter']['values'] as $key=>$val)
                                                        <option value="{{ $val }}">{{ $val }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                @break
                            @endswitch
                        @endif
                    </th>
                @else
                    @if($loop->iteration == 1)
                        <th class="select-rows-header text-center">
                            <input type="checkbox" id="select-rows">
                            <label for="select-rows">Select all</label>
                        </th>
                    @else
                        <th></th>
                    @endif
                @endif
            @endforeach
        </tr>
    @endempty


    @empty(!$columns)
        <tr class="header">
            @foreach($columns as $_index => $column)
                <th id="th-{{ $_index }}" {{ !isset($column['class']) ? "" : " class=".$column['class'] }}>{{ (isset($column['title']) ? $column['title'] : $_index) }}</th>
            @endforeach
        </tr>
    @endempty
    </thead>

    <tbody>
    @if($deferLoading == true)
        @foreach($data['data'] as $d)
            <tr>
                @empty(!$columns)
                    @foreach($columns as $_index => $column)
                        <td id="td-{{ $_index }}" {{ !isset($column['class']) ? "" : " class=".$column['class'] }}>{!!  isset($d[$_index]) ? $d[$_index] : ''  !!}</td>
                    @endforeach
                @endempty
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
