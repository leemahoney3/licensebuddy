<div class="card">
    <div class="card-body text-center">
        <div class="row">
            <div class="col-md-12 pb-2">
                <h4>License Verification</h4>
            </div>
            <div class="col-md-12 pb-3">
                <p>Use this tool to verify if a domain or IP address is licensed to use our software.</p>
            </div>
        </div>

        <div id="result" style="display: none;" class="alert alert-success col-md-8 offset-md-2">
            
        </div>

        <form action="index.php?m=licensebuddy&action=verify" method="post">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="input-group pb-3">
                        <input type="text" name="domain" id="domain" class="form-control" placeholder="mydomain.com">
                        <div class="input-group-append">
                            <button type="submit" id="check" class="btn btn-default">Verify</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mt-4">
            <div class="col-md-12">
                <p>Please report any instances of Piracy to us for further investigation</p>
            </div>
        </div>
        
    </div>
</div>
<script>
    $(document).ready(function () {

        $('#check').click(function (e) {
            e.preventDefault();

            let domain = $('#domain').val();

                var matches = domain.match(/^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/i);
                
                domain = (matches && matches[1] != null) ? matches[1] : domain;
                $('#domain').val(domain);

                $.ajax({
                    type: 'POST',
                    url: 'index.php?m=licensebuddy&action=verify',
                    data: 'domain=' + domain,
                    beforeSend: function () {
                        $('#check').html('Checking...');
                    },
                    success: function (res) {
                        res = JSON.parse(res);
                        console.log(res.valid);
                        if (res.valid) {
                            $('#result').removeClass('alert-danger');
                            $('#result').addClass('alert-success');
                        } else {
                            $('#result').removeClass('alert-success');
                            $('#result').addClass('alert-danger');
                        }

                        $('#result').html(res.message);
                        $('#result').slideDown();
                        
                    },
                    complete: function () {
                        $('#check').html('Verify');
                    }
                });

        });

    });
</script>