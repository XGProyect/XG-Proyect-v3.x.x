<tr height="20">
    <th role="cell" colspan="2">
        <select name="{select}" id="{select}" onChange="javascript:setTarget(returnValue(0, this), returnValue(1, this), returnValue(2, this), returnValue(3, this)); shortInfo();">
            <option value="0"></option>
            {options}
            <option value="{value}"{selected}>{title}</option>
            {/options}
        </select>
    </th>
</tr>