<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<form action="" method="POST">
    <input type="hidden" name="s" value="{s}">
    <table width="519">
        <tr>
            <td class="c" colspan="2">{title}</td>
        </tr>
        <tr>
            <th>{nt_priority}</th>
            <th>
                <select name="u">
                    <option value="2" {selected_2}>{nt_important}</option>
                    <option value="1" {selected_1}>{nt_normal}</option>
                    <option value="0" {selected_0}>{nt_unimportant}</option>
                </select>
            </th>
        </tr>
        <tr>
            <th>{nt_subject_note}</th>
            <th>
                <input type="text" name="title" size="30" maxlength="30" value="{subject}">
            </th>
        </tr>
        <tr>
            <th>{nt_note} (<span id="cntChars">0</span> / 5000 {nt_characters})</th>
            <th>
                <textarea name="text" cols="60" rows="10" onkeyup="javascript:cntchar(5000)">{text}</textarea>
            </th>
        </tr>
        <tr>
            <td class="c"><a href="game.php?page=notes">{nt_back}</a></td>
            <td class="c">
                <input type="reset" value="{nt_reset}">
                <input type="submit" value="{nt_save}">
            </td>
        </tr>
    </table>
</form>