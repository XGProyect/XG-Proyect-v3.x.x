

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{pr_title}</h1>
        <br />
        <form name="" method="post" action="">
            <label>
                <strong>{pr_pay_url}</strong>
            </label>
            <textarea name="premium_url" class="field span12" cols="75" rows="5">{premium_url}</textarea>

            <label>
                <strong>{pr_trader}</strong>
            </label>
            <input type="text" name="merchant_price" value="{merchant_price}" class="input-small">
            <div align="center">
                <input type="submit" name="save" value="{pr_save_changes}" class="btn btn-primary">
            </div>
        </form>
    </div>
</div><!--/span-->
