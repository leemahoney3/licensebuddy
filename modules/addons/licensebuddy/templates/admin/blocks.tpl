{extends file='admin/layouts/app.tpl'}

{block "content"}

    {if PageHelper::getAttribute('success')}
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong> Your action has been completed successfully
                </div>
            </div>
        </div>
    {/if}

    <div class="row">
        <div class="col-md-6">
            <h2>Search Blocks</h2>
            <div class="panel panel-default margin-top-lg">
                <div class="panel-body">
                    <form action="{$moduleLink}&page=blocks" method="post" class="form-inline">

                        <div class="form-group margin-right-lg">
                            <label for="domainIp">Domain Name/IP Address</label>
                            <input type="text" name="domain_ip" class="form-control" id="domainIp" value="{$request.domain_ip}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="reason">Reason</label>
                            <input type="text" name="reason" class="form-control" id="reason" value="{$request.reason}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Search Blocks</button>
                        <a href="{$moduleLink}&page=blocks" class="btn btn-default">Clear</a>
                        
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
        <h2>Add Block</h2>
            <div class="panel panel-default margin-top-lg">
                <div class="panel-body">
                    <form action="{$moduleLink}&page=blocks" method="post" class="form-inline">

                        <div class="form-group margin-right-lg">
                            <label for="addDomainIp">Domain Name/IP Address</label>
                            <input type="text" name="add_domain_ip" class="form-control" id="addDomainIp" value="{$request.add_domain_ip}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="addReason">Reason</label>
                            <input type="text" name="add_reason" class="form-control" id="addReason" value="{$request.add_reason}">
                        </div>
                        
                        <input type="submit" class="btn btn-primary" name="save" value="Add Block" />
                    
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {if $search}
                <h2>Search Results</h2>
            {else}
                <h2>All Blocks</h2>
            {/if}
            
        </div>
    </div>

    <div class="row margin-top-lg">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           <th scope="col">ID</th>
                           <th scope="col">Date/Time</th>
                           <th scope="col">Domain Name/IP Address</th>
                           <th scope="col">Reason</th>
                           <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if count($blocks.data) == 0}
                            <tr>
                                <td colspan="11" class="text-center">No blocks found</td>
                            </tr>
                        {else}
                            {foreach $blocks.data as $block}
                                <tr>
                                    <td>{$block->id}</td>
                                    <td>{$block->createdAt()}</td>
                                    <td>{($block->domain_ip == '') ? '-' : $block->domain_ip}</td>
                                    <td>{($block->reason == '') ? '-' : $block->reason}</td>
                                    <td><a href="{$moduleLink}&page=blocks&action=remove&id={$block->id}" class="btn btn-danger">Remove Block</a></td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
                {$logs.links}
            </div>
        </div>
    </div>

{/block}

{block "scripts"}
    <script src="{$systemURL}/modules/addons/licensebuddy/assets/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#licenseKey').selectpicker();
        });
    </script>
{/block}