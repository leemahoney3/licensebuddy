<script>
    $(document).ready(function () {
        $('#domain').find('.row').first().hide();
        $('#domain').find('p').first().hide();
        $('#domain').find('br').first().hide();

        $('.panel-toggle').on('click', function (e) {
            e.preventDefault();

            $(this).toggleClass('open');
            $($(this).attr('data-panel')).slideToggle(500);
        });
    });
</script>

<style>
    .card-reissue, .card-reissue .card-header, .card-reissue .card-body {
        border-color: #ffeeba;
    }

    .card-reissue .card-header {
        background-color: #fff3cd;
        color: #856404;
    }

    .mt-3 {
        margin-top: 20px;
    }

    .mt-5 {
        margin-top: 50px;
    }

</style>

<div class="card panel panel-default">
    <div class="card-body panel-body">

    {if $downloads}
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info text-center" role="alert">
                    <h5 class="mt-2 mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg> Latest Download</h5>
                    <p style="margin-bottom: 10px;">{$downloads.0.description|nl2br}</p>
                    <a href="{$downloads.0.link}" class="btn btn-primary">Download Now</a>
                </div>
            </div>
        </div>
    {/if}

        <div class="row">
            <div class="col-md-12 text-center">
                <div class="card">
                    <div class="card-body">
                        {if $status == 'Active'}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: #155724;width: 75px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h4 class="mt-2">Your License is Active</h4>
                            <p class="mt-3">Please find your license details listed below</p>
                        {/if}
                        {if $status == 'Reissued'}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: #004085;width: 75px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <h4 class="mt-2">Your License has been Reissued</h4>
                            <p class="mt-3">License details will be updated on the next validation check</p>
                        {/if}
                        {if $status == 'Suspended'}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: #856404; width: 75px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h4 class="mt-2">Your License has been Suspended</h4>
                            <p class="mt-3">Please contact support for further assistance</p>
                        {/if}
                        {if $status == 'Expired'}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: #721c24; width: 75px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h4 class="mt-2">Your License has Expired</h4>
                            <p class="mt-3">You may purchase a new license from our store</p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mt-4 mb-3">License Details</h4>
        <hr>
        <div class="row mt-3">
            <div class="col-md-12">
                <table class="table table-bordered text-left">
                    <tr>
                        <th scope="col">License Key</th>
                        <td>{$licenseKey}</td>
                    </tr>
                    <tr>
                        <th scope="col">Allowed Domains</th>
                        <td>
                            <ul style="list-style-type: none;padding-left: 0;line-height:30px;">
                                {foreach $allowedDomains as $domain}
                                    <li>{$domain}</li>
                                {/foreach}
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Allowed IP Address</th>
                        <td>{$allowedIpAddress}</td>
                    </tr>
                    <tr>
                        <th scope="col">Allowed Directory</th>
                        <td>{$allowedDirectory}</td>
                    </tr>
                    {if $isTrial}
                        <tr>
                            <th scope="col">Trial License</th>
                            <td><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: green; width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></td>
                        </tr>
                        <tr>
                            <th scope="col">Trial Expiry</th>
                            <td>{$trialExpiryDate} ({if $trialExpiryDays == 1}1 day{else if $trialExpiryDays == 'expired'}Expired{else}{$trialExpiryDays} days{/if})</td>
                        </tr>
                    {/if}
                </table>
            </div>
        </div>

        {if $allowReissue && $status == 'Active'}

            <div class="panel card card-reissue text-left">
                <div class="card-header panel-heading">
                Reissue Your License
                </div>
                <div class="card-body panel-body">
                    <div class="row">
                        <div class="col-md-10">
                            <p class="card-text">Reissuing your license will allow you to change where it's installed. The installation environmnet will be updated the next time the license is validated.</p>
                        </div>
                        <div class="col-md-2">
                            <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=reissueLicense" class="btn btn-warning">Reissue</a>
                        </div>
                    </div>
                </div>
            </div>

        {/if}
        
        {if $configurableoptions}
            
            <div class="row mt-5">
                <div class="col-md-6 text-left"><h4 class="">Configurable Items</h4></div>
            </div>
            <hr>
                
            <div class="col-md-12">
                <div class="alert alert-info">
                    {foreach $configurableoptions as $option}
                        <div class="row">
                            <div class="col-xs-5 col-5 text-right">
                                <strong>{$option.optionname}:</strong>
                            </div>
                            <div class="col-xs-7 col-7">
                                {if $option.optiontype == 3}
                                    {if $option.selectedqty}
                                        {$LANG.yes}
                                    {else}
                                        {$LANG.no}
                                    {/if}
                                {elseif $option.optiontype == 4}
                                    {$option.selectedqty} x {$config.selectedoption}
                                {else}
                                    {$option.selectedoption}
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div> 
            
        {/if}


        <div class="row mt-5">
            <div class="col-md-6 text-left"><h4 class="">Service Details</h4></div>
        </div>
        <hr>

        <div class="row mt-3">
            <div class="col-sm-4 text-center mt-3">
                <div class="card" style="height: 100%;">
                    <div class="card-body">
                        <div class="card-title"><h5>Purchase Date</h5></div>
                        <div class="card-text">{$regdate}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 text-center mt-3">
                <div class="card" style="height: 100%;">
                    <div class="card-body">
                        <div class="card-title"><h5>Next Due Date</h5></div>
                        <div class="card-text">{$nextduedate}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 text-center mt-3">
                <div class="card" style="height: 100%;">
                    <div class="card-body">
                        <div class="card-title"><h5>Billing Cycle</h5></div>
                        <div class="card-text">{$billingcycle}</div>
                    </div>
                </div>
            </div>
                
            {if $firstpaymentamount neq $recurringamount}
                <div class="col-sm-4 text-center mt-3">
                    <div class="card" style="height: 100%;">
                        <div class="card-body">
                            <div class="card-title"><h5>First Payment Amount</h5></div>
                            <div class="card-text">{$firstpaymentamount}</div>
                        </div>
                    </div>
                </div>
            {/if}
            {if $billingcycle != $LANG.orderpaymenttermonetime && $billingcycle != $LANG.orderpaymenttermfreeacount}
                <div class="col-sm-4 text-center mt-3">
                    <div class="card" style="height: 100%;">
                        <div class="card-body">
                            <div class="card-title"><h5>Recurring Amount</h5></div>
                            <div class="card-text">{$recurringamount}</div>
                        </div>
                    </div>
                </div>
            {/if}
            {if $firstpaymentamount neq $recurringamount || ($billingcycle != $LANG.orderpaymenttermonetime && $billingCycle != $LANG.orderpaymenttermfreeaccount)}
                <div class="col-sm-4 text-center mt-3">
                    <div class="card" style="height: 100%;">
                        <div class="card-body">
                            <div class="card-title"><h5>Payment Method</h5></div>
                            <div class="card-text">{$paymentmethod}</div>
                        </div>
                    </div>
                </div>
            {/if}
                
            {if $customfields}
                {foreach $customfields as $field}
                    <div class="col-sm-4 text-center mt-3">
                        <div class="card" style="height: 100%;">
                            <div class="card-body">
                                <div class="card-title"><h5>{$field.name}</h5></div>
                                <div class="card-text">{if $field.value}{$field.value}{else}-{/if}</div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}

        </div>
    </div>
</div>