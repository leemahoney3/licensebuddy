{extends file='admin/layouts/app.tpl'}

{block "content"}

    <div class="row">
        <div class="col-md-12">
            <h2>Search Access Logs</h2>
            <div class="panel panel-default margin-top-lg">
                <div class="panel-body">
                    <form action="{$moduleLink}&page=logs" method="post" class="form-inline">

                        <div class="form-group margin-right-lg">
                            <label for="licenseKey">License Key</label>
                            <select name="license_key" class="form-control" id="licenseKey" data-live-search="true">
                                <option value="">Any License Key</option>
                                {foreach $searchForm.licenses as $license}
                                    <option value="{$license->id}" {if $request.license_key == $license->id}selected{/if}>{$license->license_key}</option>
                                {/foreach}
                            </select>
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="domain">Domain Name</label>
                            <input type="text" name="domain" class="form-control" id="domain" value="{$request.domain}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="directory">Directory</label>
                            <input type="text" name="directory" class="form-control" id="directory" value="{$request.directory}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="ipAddress">IP Address</label>
                            <input type="text" name="ip_address" class="form-control" id="ipAddress" value="{$request.ip_address}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="description">Description</label>
                            <input type="text" name="description" class="form-control" id="description" value="{$request.description}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Search Logs</button>
                        <a href="{$moduleLink}&page=logs" class="btn btn-default">Clear</a>
                        
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
                <h2>All Access Logs</h2>
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
                           <th scope="col">License Key</th>
                           <th scope="col">Domain</th>
                           <th scope="col">IP Address</th>
                           <th scope="col">Directory</th>
                           <th scope="col">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if count($logs.data) == 0}
                            <tr>
                                <td colspan="11" class="text-center">No logs found</td>
                            </tr>
                        {else}
                            {foreach $logs.data as $log}
                                <tr>
                                    <td>{$log->id}</td>
                                    <td>{$log->createdAt()}</td>
                                    <td>{if $log->license_id == 0}-{else}<a href="{$moduleLink}&page=manage&id={$log->license->id}" target="_blank">{$log->license->license_key}</a>{/if}</td>
                                    <td>{($log->domain == '') ? '-' : $log->domain}</td>
                                    <td>{($log->directory == '') ? '-' : $log->directory}</td>
                                    <td>{($log->ip_address == '') ? '-' : $log->ip_address}</td>
                                    <td>{$log->description}</td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
            {$logs.links}
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