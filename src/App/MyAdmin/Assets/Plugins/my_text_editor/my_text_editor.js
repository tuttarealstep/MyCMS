'use strict';

if (theme_var == "") {
    theme_var = "default";
    addInfo(_t_theme_not_found);
}

var cont_bck = "";
var c_file_path = "";

var div_code_mirror = CodeMirror.fromTextArea(document.getElementById("div_code_mirror"),
    {
        lineNumbers: true,
        matchBrackets: true,
        styleActiveLine: true,
        mode: "application/x-httpd-php",
        theme: "dracula",
        scrollbarStyle: "simple",
        indentUnit: 4,
        indentWithTabs: true,
        hint: true,
        autohint: true,
        autoCloseTags: true,
        autoCloseBrackets: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "Ctrl-P": function (cm) {
                if (cm.openDialog) {
                    //cm.openDialog('Menu: <input type="text" style="width: 10em" class="CodeMirror-search-field"/> <span style="color: #888888" class="CodeMirror-search-hint">(Use /re/ syntax for regexp search)</span>');
                }
            },
            "Alt-F": "findPersistent",
            "Ctrl-S": saveCurrentFile

        }
    });

function newFile() {
    var new_file_b = false;

    var confirm_v = confirm(_t_create_new_file);
    if (confirm_v == true) {
        new_file_b = true;
    }

    if (new_file_b == true) {
        var file_name = prompt(_t_file_name + "\n/src/App/Content/Theme/" + theme_var + "/");
        if (file_name != null) {
            $.post(myBasePath + '/src/App/Content/Ajax/code_editor_new_file.php', {
                file_name: Base64.encode(file_name),
                theme_v: Base64.encode(theme_var)
            }, function (data) {
            });
            addInfo(_t_file_created);
            reloadFolder("#file_manager", theme_var);
        } else {
            alert(_t_file_not_created);
        }
    }
}

function saveCurrentFile() {
    var save_file_b = false;

    var confirm_v = confirm(_t_save_file);
    if (confirm_v == true) {
        save_file_b = true;
    }

    if (save_file_b == true) {
        if (c_file_path != "") {
            $.post(myBasePath + '/src/App/Content/Ajax/code_editor_save_file.php', {
                file_c: Base64.encode(div_code_mirror.getValue()),
                file_p: Base64.encode(c_file_path)
            }, function (data) {
            });
            addInfo(_t_file_saved);
        }
    }
}


function printEditorMode(obj) {
    document.getElementById("editorMode").innerHTML = obj.getOption("mode");
}

function printLineInfo(line, ch) {
    document.getElementById("lineInfo").innerHTML = _t_line + " " + line + ", " + _t_column + " " + ch;
}

function addInfo(text) {
    document.getElementById("editorInfo").innerHTML = text;
    console.log(text);
}

function setTitle(text) {
    document.getElementById("projectTitle").innerHTML = text;
}

function loadFolder(html_obj, folder) {
    $.post(myBasePath + '/src/App/Content/Ajax/code_editor_tree_view.php', {dir: folder}, function (data) {

        $(html_obj).append(data);
        if (theme_var == folder) {
            setTitle(theme_var);
            $(html_obj).find('UL:hidden').show();
        } else {
            $(html_obj).find('UL:hidden').slideDown({duration: 500, easing: null});
        }

    });
}

function reloadFolder(html_obj, folder) {
    $.post(myBasePath + '/src/App/Content/Ajax/code_editor_tree_view.php', {dir: folder}, function (data) {
        $(html_obj).empty();
        $(html_obj).append(data);
        if (theme_var == folder) {
            setTitle(theme_var);
            $(html_obj).find('UL:hidden').show();
        } else {
            $(html_obj).find('UL:hidden').slideDown({duration: 500, easing: null});
        }

    });
}

function changeMode(editor, mode) {
    editor.setOption("mode", mode);
    printEditorMode(editor);
}

function loadFile(editor, file) {
    var load_file_b = false;

    if (cont_bck != Base64.encode(editor.getValue())) {
        var confirm_v = confirm(_t_if_you_confirm);
        if (confirm_v == true) {
            load_file_b = true;
        }
    } else {
        load_file_b = true;
    }

    if (load_file_b) {
        var file_ext = file.split('.').pop();
        $.post(myBasePath + '/src/App/Content/Ajax/code_editor_file_view.php', {file: file}, function (data) {
            cont_bck = data;
            c_file_path = file;
            editor.getDoc().setValue(Base64.decode(data));
            switch (file_ext) {
                case 'xml':
                    changeMode(editor, "xml");
                    break;
                case 'js':
                    changeMode(editor, "javascript");
                    break;
                case 'php':
                    changeMode(editor, "php");
                    break;
                case 'css':
                    changeMode(editor, "css");
                    break;
                case 'twig':
                    changeMode(editor, "twig");
                    break;
                case 'png':
                    editorShowImage("/src/App/Content/Theme/" + file);
                    changeMode(editor, "png");
                    break;
                default:
                    alert(file_ext)
                    changeMode(editor, "application/x-httpd-php");
                    break;
            }
        });
    }
}

function editorShowImage(src) {
    var img = document.createElement("img");
    img.src = src;

    document.body.appendChild(img);
}

$("#file_manager").on('click', 'LI A', function () { /* monitor the click event on links */
    var entry = $(this).parent();
    /* get the parent element of the link */
    if (entry.hasClass('folder')) { /* check if it has folder as class name */
        if (entry.hasClass('collapsed')) { /* check if it is collapsed */
            entry.find('UL').remove();
            /* if there is any UL remove it */
            loadFolder(entry, escape($(this).attr('rel')));
            /* initiate Ajax request */
            entry.removeClass('collapsed').addClass('expanded');
            /* mark it as expanded */
        }
        else { /* if it is expanded already */
            entry.find('UL').slideUp({duration: 500, easing: null});
            /* collapse it */
            entry.removeClass('expanded').addClass('collapsed');
            /* mark it as collapsed */
        }
    } else {
        loadFile(div_code_mirror, $(this).attr('rel'));
        console.log("File:  " + $(this).attr('rel'));
        addInfo(_t_file_loaded);
    }
    return false;
});

function getFileExt(file)
{
    return (/[.]/.exec(file)) ? /[^.]+$/.exec(file)[0] : undefined;
}

div_code_mirror.on('cursorActivity', function (e) {
    var line = e.doc.getCursor().line;
    var ch = e.doc.getCursor().ch;

    printLineInfo(line + 1, ch);
});

/* Last Settings | Setup */
div_code_mirror.setOption("fullScreen", !div_code_mirror.getOption("fullScreen"));
printEditorMode(div_code_mirror);
printLineInfo(0, 0);
loadFolder("#file_manager", theme_var);
addInfo(_t_setup_complete);

$('#file_manager').perfectScrollbar();
