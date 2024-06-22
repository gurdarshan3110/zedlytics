<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .ledger-table {
        overflow-y: auto;
        position: relative;
    }
</style>
<div class="row">
      <div class="col">
        <div class="table-responsive">
            <!-- Excel-like grid -->
            <table class="table table-bordered w-100 ledger-table" id="excel-head">
              <tbody>
                <tr>
                  <th class="excel-cell">Account ID</th>
                  <th class="excel-cell">Date</th>
                  <th class="excel-cell">Utr No.</th>
                  <th class="excel-cell text-end">Credit</th>
                  <th class="excel-cell text-end">Debit</th>
                  <th class="excel-cell text-end">Balance</th>
                  <th class="excel-cell">Remarks</th>
                  <th class="excel-cell">Name</th>
                  <th class="excel-cell">Type</th>
                  <th class="hide-cell">Type</th>
                  
                </tr>
              </tbody>
            </table>
        </div>
        <div class="table-responsive h-460">
            <table class="table table-bordered w-100 ledger-table" id="excel-grid">
              <tbody id="data-container">
                @if($ledger!=null)
                  @foreach($ledger as $row)
                  <tr class="{{(($row->account_type==\App\Models\CashbookLedger::ACCOUNT_TYPE_CLIENT_VAL)?'client-row':(($row->account_type==\App\Models\CashbookLedger::ACCOUNT_TYPE_BANK_VAL)?'bank-row':'party-row'))}}">
                    <td class="excel-cell" contenteditable="true">{{$row->account_code}}</td>
                    <td class="excel-cell" contenteditable="true">{{\Carbon\Carbon::parse($row->ledger_date)->format('d/m/Y')}}</td>
                    <td class="excel-cell" contenteditable="true">{{$row->utr_no}}</td>
                    <td class="excel-cell text-end" contenteditable="true">{{(($row->type==App\Models\CashbookLedger::LEDGER_TYPE_CREDIT_VAL)?$row->amount:'')}}</td>
                    <td class="excel-cell text-end" contenteditable="true">{{(($row->type==App\Models\CashbookLedger::LEDGER_TYPE_DEBIT_VAL)?abs($row->amount):'')}}</td>
                    <td class="excel-cell text-end">{{$row->current_balance}}</td>
                    <td class="excel-cell" contenteditable="true">{{$row->remarks}}</td>
                    <td class="excel-cell"></td>
                    <td class="excel-cell"></td>
                    <td class="hide-cell">{{$row->transaction_id}}</td>
                  </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
            <div id="hint-container"></div>
        </div>
      </div>
    </div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date').on('change', function() {
            var selectedDate = $(this).val();
            var bank = '{{$bankId}}';
            
            $.ajax({
                url: '/pool/data/'+selectedDate+'/'+bank,  
                type: 'GET',
                //data: { date: selectedDate },
                success: function(response) {
                    response = JSON.parse(response)
                    $('#data-container').html(response.html);
                    $('#bank_' + bank).val(response.balance)
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    $('#data-container').html('<p>Failed to fetch data. Please try again.</p>');
                }
            });
        });
        function saveCellValues(data) {
            //console.log(data);
            $('#response-status').removeClass('green-blinking');
            $('#response-status').addClass('red-blinking');
            var bank = '{{$bankId}}';
            //var date = $('#date').val();
            data.push(bank);
            //data.push(date);
            $.ajax({
                url: '{{ route("save.pool") }}',
                type: 'POST',
                dataType: 'json',
                data: JSON.stringify(data), 
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                  $('#response-status').addClass('green-blinking');
                  $('#response-status').removeClass('red-blinking');
                   var transactionId = response.transaction_id;
                    var backgroundColor = response.background;
                    
                    // Find the row containing the cell with the hidden transaction_id
                    var row = $('td.hide-cell').filter(function() {
                        return $(this).text() == transactionId;
                    }).closest('tr');
                    
                    // Change the background color of the row
                    //row.className= backgroundColor;
                    row.addClass(backgroundColor);
                },
                error: function(xhr, status, error) {
                  $('#response-status').addClass('green-blinking');
                  $('#response-status').removeClass('red-blinking');
                }
            });
        }
        
        $('#excel-grid').on('keydown', '.excel-cell', function(event) {
            var $currentCell = $(this);
            var $currentRow = $currentCell.closest('tr');
            var $cells = $currentRow.find('.excel-cell');
            var currentIndex = $cells.index($currentCell);
            var numRows = $('#excel-grid tbody tr').length;
            var numCols = $cells.length;

            // Handle arrow key navigation
            switch (event.which) {
                case 37: // Left arrow
                    if (currentIndex > 0) {
                        $cells.eq(currentIndex - 1).focus();
                    }
                    break;
                case 38: // Up arrow
                    var $prevRow = $currentRow.prev();
                    if ($prevRow.length > 0) {
                        $prevRow.find('.excel-cell').eq(currentIndex).focus();
                    }
                    break;
                case 39: // Right arrow
                    if (currentIndex < numCols - 1) {
                        $cells.eq(currentIndex + 1).focus();
                    }
                    break;
                case 40: // Down arrow
                    var $nextRow = $currentRow.next();
                    if ($nextRow.length > 0) {
                        $nextRow.find('.excel-cell').eq(currentIndex).focus();
                    }
                    break;
                case 13: // Enter key
                    event.preventDefault();
                    if ($currentCell.is(':focus')) {
                        if ($currentCell.closest('td').is(':nth-child(3)')) {
                            if ($currentCell.text() != '') {
                                // var newRow = '<tr>';
                                // var i = 0;

                                // $('#excel-head tbody th').each(function() {
                                //     cellValue = Math.floor(Date.now() / 1000);
                                //     newRow += '<td class="excel-cell ' + ((i == 8) ? 'hide-cell' : '') + (((i == 2) || (i == 3) || (i == 4)) ? 'text-end' : '') + '" '+(((i == 6) || (i == 7) || (i == 4)) ? '' : 'contenteditable="true"')+'></td>';
                                //     i++;
                                // });
                                // newRow += '</tr>';
                                // $('#excel-grid tbody').append(newRow);
                                // $nextRow = $currentRow.next(); // Get the newly created row
                                // $nextRow.find('.excel-cell').eq(0).focus(); 
                            }else{
                              $nextCell = $cells.eq(currentIndex + 1);
                              $nextCell.focus();
                            }
                        }else if ($currentCell.closest('td').is(':nth-child(4)')) {
                            if ($currentCell.text() != '') {
                                // var newRow = '<tr>';
                                // var i = 0;
                                // $('#excel-head tbody th').each(function() {
                                //     cellValue = Math.floor(Date.now() / 1000);
                                //     newRow += '<td class="excel-cell ' + ((i == 8) ? 'hide-cell' : '') + (((i == 2) || (i == 3) || (i == 4)) ? 'text-end' : '') + '" '+(((i == 6) || (i == 7) || (i == 4)) ? '' : 'contenteditable="true"')+'></td>';
                                //     i++;
                                // });
                                // newRow += '</tr>';
                                // $('#excel-grid tbody').append(newRow);
                                $nextRow = $currentRow.next(); // Get the newly created row
                                $nextRow.find('.excel-cell').eq(0).focus(); // Set focus to the first cell in the new row
                            }else{
                              $nextCell = $cells.eq(currentIndex + 1);
                              $nextCell.focus();
                            }
                        }else if ($currentCell.closest('td').is(':nth-child(6)')) {
                                var newRow = '<tr>';
                                var i = 0;
                                $('#excel-head tbody th').each(function() {
                                    cellValue = Math.floor(Date.now() / 1000);
                                    newRow += '<td class="excel-cell ' + ((i == 8) ? 'hide-cell' : '') + (((i == 2) || (i == 3) || (i == 4)) ? 'text-end' : '') + '" '+(((i == 6) || (i == 7) || (i == 4)) ? '' : 'contenteditable="true"')+'></td>';
                                    i++;
                                });
                                newRow += '</tr>';
                                $('#excel-grid tbody').append(newRow);
                                $nextRow = $currentRow.next(); // Get the newly created row
                                $nextRow.find('.excel-cell').eq(0).focus(); // Set focus to the first cell in the new row
                            
                        }else{
                          var $nextCell;
                          if (currentIndex === numCols - 1) {
                              $nextCell = $currentRow.next().find('.excel-cell').first();
                          } else {
                              $nextCell = $cells.eq(currentIndex + 1);
                          }
                          //saveCellValues();
                          $nextCell.focus();
                        }
                    }
                    break;
            }
        });


        var initialCellValues = {};

        $('#excel-grid .excel-cell').each(function() {
            var cellId = $(this).closest('tr').index() + '-' + $(this).index();
            initialCellValues[cellId] = $(this).text();
        });

        $('#excel-grid').on('blur', 'tbody tr td', function() {
            $('.excel-cell[contenteditable="true"]').on('focus', function() {
                $(this).closest('tr').addClass('highlight-row');
                $hideCell=$(this).closest('tr').find('.hide-cell');
                if($hideCell.text()==''){
                    cellValue = Math.floor(Date.now() / 1000);
                    $hideCell.text(cellValue);
                }
            }).on('blur', function() {
                $(this).closest('tr').removeClass('highlight-row');
            });
            var $currentCell = $(this);
            var cellId = $currentCell.closest('tr').index() + '-' + $currentCell.index();
            var currentValue = $currentCell.text();
            var initialValue = initialCellValues[cellId];
            if (currentValue !== initialValue) {
                var data = [];
                var i = 0;

                $currentCell.closest('tr').find('.excel-cell').each(function(index) {
                    var cellValue = $(this).text();
                    data.push(cellValue);
                    i++;
                });
                if(data[8]=='' || data[8]==null){
                    data.push($currentCell.closest('tr').find('.hide-cell').text());
                }
                saveCellValues(data);
                calculateBalance();
            }
        });
    });
    function calculateBalance() {
        var totalCredit = 0;
        var totalDebit = 0;
        var bank = '{{$bankId}}';
        var initialBalance = parseFloat($('#bank_' + bank).val()); 
        var balance = isNaN(initialBalance) ? 0 : initialBalance;
        $('#excel-grid tbody tr').each(function() {
            var credit = parseFloat($(this).find('.excel-cell').eq(3).text());
            var debit = parseFloat($(this).find('.excel-cell').eq(4).text());
            
            if (!isNaN(credit)) {
                totalCredit += credit;
            }
            if (!isNaN(debit)) {
                totalDebit += debit;
            }
            
            balance = initialBalance + totalCredit - totalDebit;
            $(this).find('.excel-cell').eq(5).text(balance.toFixed(2));
        });
    }



    $(document).ready(function() {
        var $hintContainer = $('#hint-container');
        var currentElement = null;
        var selectedIndex = -1;

        function showHints(element, hints) {
            $hintContainer.empty().hide();
            selectedIndex = -1;
            currentElement = element;

            if (hints.length > 0) {
                var $tableContainer = $('.ledger-table');
                var offset = $(element).offset();
                var containerOffset = $tableContainer.offset();

                if (offset && containerOffset) {
                    var relativeTop = offset.top - containerOffset.top + $tableContainer.scrollTop();
                    var height = $(element).outerHeight();
                    var width = $(element).outerWidth();

                    $hintContainer.css({
                        top: relativeTop + 35 +'px',
                        //left: offset.left - containerOffset.left,
                        width: width,
                        background: '#fff',
                        position: 'absolute',
                    });

                    var hintList = $('<div>').addClass('hint-list');

                    hints.forEach(function(hint, index) {
                        var hintItem = $('<div>').addClass('hint-item');
                        hintItem.text(hint);
                        hintItem.on('click', function() {
                            //alert(hint);
                            $(element).text(hint);
                            $hintContainer.empty().hide();
                        });
                        hintList.append(hintItem);
                    });

                    $hintContainer.append(hintList).show();
                } else {
                    console.error('Offsets are undefined:', { offset, containerOffset });
                }
            }
        }

        $('#data-container').on('input', 'tr td:first-child[contenteditable="true"]', function() {
            var query = $(this).text();

            if (query.length > 0) {
                $.ajax({
                    url: '/hints',
                    data: { query: query },
                    success: function(data) {
                        showHints(this, data);
                    }.bind(this),
                    error: function(err) {
                        console.error('Error fetching hints:', err);
                    }
                });
            } else {
                $hintContainer.empty().hide();
            }
        });

        // Hide hints when moving to the next cell
        $('#data-container').on('focusout', 'tr td:first-child[contenteditable="true"]', function() {
            //$hintContainer.empty().hide();
        });

        $('#data-container').on('focusin', 'tr td:first-child[contenteditable="true"]', function() {
            $(document).on('keydown.hintNavigation', function(e) {
                if ($hintContainer.is(':visible') && currentElement) {
                    var hintItems = $hintContainer.find('.hint-item');

                    if (e.key === 'ArrowDown') {
                        selectedIndex = (selectedIndex + 1) % hintItems.length;
                        hintItems.removeClass('selected');
                        $(hintItems[selectedIndex]).addClass('selected');
                        e.preventDefault();
                    } else if (e.key === 'ArrowUp') {
                        selectedIndex = (selectedIndex - 1 + hintItems.length) % hintItems.length;
                        hintItems.removeClass('selected');
                        $(hintItems[selectedIndex]).addClass('selected');
                        e.preventDefault();
                    } else if (e.key === 'Enter' && selectedIndex !== -1) {
                        var selectedHint = $(hintItems[selectedIndex]).text();
                        $(currentElement).text(selectedHint);
                        $hintContainer.empty().hide();
                        e.preventDefault();
                    } else if (e.key === 'Escape') {
                        $hintContainer.empty().hide();
                        e.preventDefault();
                    }
                }
            });
        });

        // Remove keydown event handler when focusing out of the cell
        $('#data-container').on('focusout', 'tr td:first-child[contenteditable="true"]', function() {
            $(document).off('keydown.hintNavigation');
        });

        // Close hint list when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.hint-list, .excel-cell').length) {
                $hintContainer.empty().hide();
            }
        });
    });
</script>

