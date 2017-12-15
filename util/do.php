<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年12月14日
 * Time: 16:30
 */
header("Content-Type:text/html;charset=utf-8");
if ($url = $_POST['url']) {

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $rs = curl_exec($ch);
    curl_close($ch);
}
?>
<style>
    label {
        display: block;
        padding: 2px 0;
    }
</style>
Ajax获取数据
<form method="post" action="do.php">
    <label><input type="text" style="width: 50%;height: 100px" name="url"><br></label>
    <label><input type="submit" value="submit"></label>
</form>
<form id="form1" name="form1">
    <textarea name="sourcejson" id="sourcejson" cols="80"
              rows="30"><?php echo ltrim(rtrim(stripslashes(json_encode($rs)), "\""), "\"") ?></textarea>
    <input type="button" onclick="formatJson();" value="美化"/>
    <textarea name="targetjson" id="targetjson" cols="80" rows="30">  </textarea>
</form>
<script>
    function repeat(s, count) {
        return new Array(count + 1).join(s);
    }

    function formatJson() {

        var json = document.form1.sourcejson.value;

        var i = 0,
            len = 0,
            tab = "    ",
            targetJson = "",
            indentLevel = 0,
            inString = false,
            currentChar = null;


        for (i = 0, len = json.length; i < len; i += 1) {
            currentChar = json.charAt(i);

            switch (currentChar) {
                case '{':
                case '[':
                    if (!inString) {
                        targetJson += currentChar + "\n" + repeat(tab, indentLevel + 1);
                        indentLevel += 1;
                    } else {
                        targetJson += currentChar;
                    }
                    break;
                case '}':
                case ']':
                    if (!inString) {
                        indentLevel -= 1;
                        targetJson += "\n" + repeat(tab, indentLevel) + currentChar;
                    } else {
                        targetJson += currentChar;
                    }
                    break;
                case ',':
                    if (!inString) {
                        targetJson += ",\n" + repeat(tab, indentLevel);
                    } else {
                        targetJson += currentChar;
                    }
                    break;
                case ':':
                    if (!inString) {
                        targetJson += ": ";
                    } else {
                        targetJson += currentChar;
                    }
                    break;
                case ' ':
                case "\n":
                case "\t":
                    if (inString) {
                        targetJson += currentChar;
                    }
                    break;
                case '"':
                    if (i > 0 && json.charAt(i - 1) !== '\\') {
                        inString = !inString;
                    }
                    targetJson += currentChar;
                    break;
                default:
                    targetJson += currentChar;
                    break;
            }
        }
        document.form1.targetjson.value = targetJson;
        return;
    }
</script>
