<div class="row mt-2">
    <div class="col-md-12">
        <div>
            <span class="slds-text-title">Email</span>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-md-12">
        @include('inbox.table_show', ['email_show' => $emails])
    </div>
</div>
