<?

if (empty($todo_s_sort)) { $todo_s_sort = "priority"; }
$today = getdate();

$menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><FORM name=input_form ACTION=\"$toppath/todo/\" METHOD=POST>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD><TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD BGCOLOR=#999999><A HREF=\"/todo/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"/image/todo.gif\" ALIGN=ABSMIDDLE ALT=\"È÷ËºÏ¿\" BORDER=0> <A HREF=\"/todo/\"><FONT COLOR=#FFFFFF>È÷ËºÏ¿</A></TD>\n";
$menutext .= "</TR>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD STYLE=\"line-height:15px\" BGCOLOR=#FFFFFF VALIGN=TOP><TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD WIDTH=50 valign=top><nobr>Í¥ÀèÅÙ&nbsp;&nbsp;<select style=\"vertical-align: top;\" name=\"s_priority\">\n";
$selected[$todo_s_priority] = " selected";
$menutext .= "<option value=0".$selected[0].">Á´¤Æ</option>\n";
$menutext .= "<option value=3".$selected[3].">ÉáÄÌ</option>\n";
$menutext .= "<option value=2".$selected[2].">¤ä¤ä¹â</option>\n";
$menutext .= "<option value=1".$selected[1].">¹â</option></nobr></TD>\n";
unset($selected);
$menutext .= "</TR>\n";
$menutext .= "<tr height=1>\n";
$menutext .= "<td colspan=2><hr size=1></td>\n";
$menutext .= "</tr>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD valign=top>³«»ÏÆü</td>\n";
$menutext .= "</tr>\n";
$menutext .= "<tr>\n";
$menutext .= "<td valign=top align=left><nobr><input type=text name=from_begin_year value=\"".$todo_s_from_begin_year."\" style=\"width: 38px\">/<select name=from_begin_month>\n";
$selected[$todo_s_from_begin_month] = " selected";
for ($i = 1; $i <= 12; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select>/<select name=from_begin_day>\n";
$selected[$todo_s_from_begin_day] = " selected";
for ($i = 1; $i <= 31; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select></td>\n";
$menutext .= "</TR>\n";
$menutext .= "<tr>\n";
$menutext .= "<td valign=top align=right><nobr>¡Á<input type=text name=to_begin_year value=\"".$todo_s_to_begin_year."\" style=\"width: 38px\">/<select name=to_begin_month>\n";
$selected[$todo_s_to_begin_month] = " selected";
for ($i = 1; $i <= 12; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select>/<select name=to_begin_day>\n";
$selected[$todo_s_to_begin_day] = " selected";
for ($i = 1; $i <= 31; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select></td><td>\n";
$menutext .= "</select></td>\n";
$menutext .= "</TR>\n";
$menutext .= "<tr height=1>\n";
$menutext .= "<td colspan=2><hr size=1></td>\n";
$menutext .= "</tr>\n";
$menutext .= "<script language=\"javascript\">\n";
$menutext .= "function chkbox_click(f){\n";
$menutext .= "flg = f.view_end_flg.checked;\n";
$menutext .= "if (flg==true) {\n";
$menutext .= "	f.use_end_flg.value = 't';\n";
$menutext .= "} else {\n";
$menutext .= "	f.use_end_flg.value = 'f';\n";
$menutext .= "}\n";
$menutext .= "f.from_end_year.disabled = flg;\n";
$menutext .= "f.from_end_month.disabled = flg;\n";
$menutext .= "f.from_end_day.disabled = flg;\n";
$menutext .= "f.to_end_year.disabled = flg;\n";
$menutext .= "f.to_end_month.disabled = flg;\n";
$menutext .= "f.to_end_day.disabled = flg;\n";
$menutext .= "}\n";
$menutext .= "</script>\n";
$menutext .= "<TR>\n";
if ($todo_s_use_end_flg == "on"){
	$checked = " checked";
	$disabled = " disabled";
}
$menutext .= "<TD valign=top><nobr>ÄùÀÚÆü&nbsp;&nbsp;<input type=checkbox name=view_end_flg".$checked." onclick=\"chkbox_click(this.form);\">Ì¤ÀßÄê</nobr><input type=hidden name=use_end_flg VALUE=\"\"></td>\n";
$menutext .= "</tr>\n";
$menutext .= "<tr>\n";
$menutext .= "<td valign=top align=left><nobr><input type=text value=\"".$todo_s_from_end_year."\" name=from_end_year".$disabled." style=\"width: 38px\">/<select name=from_end_month".$disabled.">\n";
$selected[$todo_s_from_end_month] = " selected";
for ($i = 1; $i <= 12; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select>/<select name=from_end_day".$disabled.">\n";
$selected[$todo_s_from_end_day] = " selected";
for ($i = 1; $i <= 31; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select></td>\n";
$menutext .= "</TR>\n";
$menutext .= "<tr>\n";
$menutext .= "<td valign=top align=right><nobr>¡Á<input type=text name=to_end_year value=\"".$todo_s_to_end_year."\"".$disabled." style=\"width: 38px\">/<select name=to_end_month".$disabled.">\n";
$selected[$todo_s_to_end_month] = " selected";
//print_r($selected);
for ($i = 1; $i <= 12; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select>/<select name=to_end_day".$disabled.">\n";
$selected[$todo_s_to_end_day] = " selected";
for ($i = 1; $i <= 31; $i++){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select></td>\n";
$menutext .= "</TR>\n";
$menutext .= "<tr height=1>\n";
$menutext .= "<td colspan=2><hr size=1></td>\n";
$menutext .= "</tr>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD valign=top>Ã£À®Î¨</td>\n";
$menutext .= "</tr>\n";
$menutext .= "<tr>\n";
$menutext .= "<td valign=top><nobr>ºÇÄã<select name=progress_min>\n";
$selected[$todo_s_progress_min] = " selected";
for ($i = 0; $i <= 100; $i = $i + 10){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select>¡ó</td>\n";
$menutext .= "</tr>\n";
$menutext .= "<tr>\n";
$menutext .= "<td align=right>¡ÁºÇ¹â<select name=progress_max>\n";
$selected[$todo_s_progress_max] = " selected";
for ($i = 0; $i <= 100; $i = $i + 10){
	$menutext .= "<option value=".$i.$selected[$i].">".$i."</option>";
}
unset($selected);
$menutext .= "</select>¡ó<nobr></td>\n";
$menutext .= "</TR>\n";
$menutext .= "<tr height=1>\n";
$menutext .= "<td colspan=2><hr size=1></td>\n";
$menutext .= "</tr>\n";
$menutext .= "<TR>\n";
$menutext .= "<TD valign=top><nobr>¥¿¥¤¥È¥ë&nbsp;&nbsp;<input type=\"text\" name=title value=\"".$todo_s_title."\" style=\"width: 70px;\">¤ò´Þ¤à</nobr><td>\n";
$menutext .= "</TR>\n";
$menutext .= "</TABLE></TD>\n";
$menutext .= "</TR>\n";
$menutext .= "</TABLE></TD>\n";
$menutext .= "</TR>\n";
$menutext .= "</TABLE></TD>\n";
$menutext .= "</TR>\n";
$menutext .= "<tr>\n";
$menutext .= "<td align=right><input type=submit value=\"¸¡º÷\"><input type=button name=reset onclick=\"javascript:input_form_reset();\"value=\"¥¯¥ê¥¢\">\n";
$menutext .= "<script language=\"javascript\">\n";
$menutext .= "function input_form_reset(){\n";
$menutext .= "document.input_form.s_priority.value=0;\n";
$menutext .= "document.input_form.from_begin_year.value=".$today["year"].";\n";
$menutext .= "document.input_form.from_begin_month.value=1;\n";
$menutext .= "document.input_form.from_begin_day.value=1;\n";
$menutext .= "document.input_form.to_begin_year.value=".$today["year"].";\n";
$menutext .= "document.input_form.to_begin_month.value=12;\n";
$menutext .= "document.input_form.to_begin_day.value=31;\n";
$menutext .= "document.input_form.s_use_end_flg.checked=\"on\";\n";
$menutext .= "document.input_form.from_end_year.value=".$today["year"].";\n";
$menutext .= "document.input_form.from_end_month.value=1;\n";
$menutext .= "document.input_form.from_end_day.value=1;\n";
$menutext .= "document.input_form.to_end_year.value=".$today["year"].";\n";
$menutext .= "document.input_form.to_end_month.value=12;\n";
$menutext .= "document.input_form.to_end_day.value=31;\n";
$menutext .= "document.input_form.progress_min.value=0;\n";
$menutext .= "document.input_form.progress_max.value=90;\n";
$menutext .= "document.input_form.title.value=\"\";\n";
$menutext .= "}\n";
$menutext .= "</script>\n";

?>