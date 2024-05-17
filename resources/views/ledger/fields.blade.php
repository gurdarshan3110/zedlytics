<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
      <div class="col">
        <div class="table-responsive">
            <!-- Excel-like grid -->
            <table class="table table-bordered w-100 ledger-table" id="excel-head">
              <tbody>
                <tr>
                  <th class="excel-cell">Account ID</th>
                  <th class="excel-cell">Utr No.</th>
                  <th class="excel-cell text-end">Credit</th>
                  <th class="excel-cell text-end">Debit</th>
                  <th class="excel-cell text-end">Balance</th>
                  <th class="excel-cell">Remarks</th>
                  <th class="excel-cell">Name</th>
                  <th class="excel-cell">Type</th>
                  
                </tr>
              </tbody>
            </table>
        </div>
        <div class="table-responsive h-460">
            <table class="table table-bordered w-100 ledger-table" id="excel-grid">
              <tbody>
                @if($ledger!=null)
                  @foreach($ledger as $row)
                  <tr>
                    <td class="excel-cell" contenteditable="true">{{$row->account_code}}</td>
                    <td class="excel-cell" contenteditable="true">{{$row->utr_no}}</td>
                    <td class="excel-cell text-end" contenteditable="true">{{(($row->type==App\Models\CashbookLedger::LEDGER_TYPE_CREDIT_VAL)?$row->amount:'')}}</td>
                    <td class="excel-cell text-end" contenteditable="true">{{(($row->type==App\Models\CashbookLedger::LEDGER_TYPE_DEBIT_VAL)?abs($row->amount):'')}}</td>
                    <td class="excel-cell text-end" contenteditable="true">{{$row->current_balance}}</td>
                    <td class="excel-cell" contenteditable="true">{{$row->remarks}}</td>
                    <td class="excel-cell"></td>
                    <td class="excel-cell"></td>
                  </tr>
                  @endforeach
                @endif
                <tr>
                  <td class="excel-cell" contenteditable="true"></td>
                  <td class="excel-cell" contenteditable="true"></td>
                  <td class="excel-cell text-end" contenteditable="true"></td>
                  <td class="excel-cell text-end" contenteditable="true"></td>
                  <td class="excel-cell text-end" contenteditable="true"></td>
                  <td class="excel-cell" contenteditable="true"></td>
                  <td class="excel-cell"></td>
                  <td class="excel-cell"></td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
<script type="text/javascript">
    $(document).ready(function() {
 
        function saveCellValues(data) {
            //console.log(data);
            $('#response-status').removeClass('green-blinking');
            $('#response-status').addClass('red-blinking');
            var bank = '{{$bankId}}';
            data.push(bank);
            $.ajax({
                url: '{{ route("save.ledger") }}',
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
                                var newRow = '<tr>';
                                var i = 0;
                                $('#excel-head tbody th').each(function() {
                                    newRow += '<td class="excel-cell ' + (((i == 2) || (i == 3) || (i == 4)) ? 'text-end' : '') + '" contenteditable="true"></td>';
                                    i++;
                                });
                                newRow += '</tr>';
                                $('#excel-grid tbody').append(newRow);
                                $nextRow = $currentRow.next(); // Get the newly created row
                                $nextRow.find('.excel-cell').eq(0).focus(); // Set focus to the first cell in the new row
                            }else{
                              $nextCell = $cells.eq(currentIndex + 1);
                              $nextCell.focus();
                            }
                        }else if ($currentCell.closest('td').is(':nth-child(4)')) {
                            if ($currentCell.text() != '') {
                                var newRow = '<tr>';
                                var i = 0;
                                $('#excel-head tbody th').each(function() {
                                    newRow += '<td class="excel-cell ' + (((i == 2) || (i == 3) || (i == 4)) ? 'text-end' : '') + '" contenteditable="true"></td>';
                                    i++;
                                });
                                newRow += '</tr>';
                                $('#excel-grid tbody').append(newRow);
                                $nextRow = $currentRow.next(); // Get the newly created row
                                $nextRow.find('.excel-cell').eq(0).focus(); // Set focus to the first cell in the new row
                            }else{
                              $nextCell = $cells.eq(currentIndex + 1);
                              $nextCell.focus();
                            }
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
            var $currentCell = $(this);
            var cellId = $currentCell.closest('tr').index() + '-' + $currentCell.index();
            var currentValue = $currentCell.text();
            var initialValue = initialCellValues[cellId];
            if (currentValue !== initialValue) {
                var data = [];
                $currentCell.closest('tr').find('.excel-cell').each(function(index) {
                    var cellValue = $(this).text();
                    data.push(cellValue);
                });
                saveCellValues(data);
                calculateBalance();
            }
        });
    });
    function calculateBalance() {
        var totalCredit = 0;
        var totalDebit = 0;
        $('#excel-grid tbody tr').each(function() {
            var credit = parseFloat($(this).find('.excel-cell').eq(2).text());
            var debit = parseFloat($(this).find('.excel-cell').eq(3).text());
            if (!isNaN(credit)) {
                totalCredit += credit;
            }
            if (!isNaN(debit)) {
                totalDebit += debit;
            }
            $(this).find('.excel-cell').eq(4).text(totalCredit - totalDebit);
        });
    }
</script>