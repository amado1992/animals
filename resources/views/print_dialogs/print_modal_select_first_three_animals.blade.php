<!-- Modal to enable selecting the first three animals -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Print options" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select the animals that should be in the e-mail</h5>
            </div>
            <div id="coverAnimalsWrapper"></div>
            <div class="modal-footer">
                <button
                    type="button"
                    id="printSurplusListButton"
                    class="btn btn-primary" 
                    onClick="getPrintedSurplus()"
                >
                    Print list
                </button>
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- // Modal to enable selecting the first three animals -->