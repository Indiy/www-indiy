

var state = 'off';
var g_editor = false;

var toolbar_config = 
{
    collapse: true,
    titlebar: 'Body',
    draggable: false,
    buttonType: 'advanced',
    buttons: [
        { group: 'fontstyle', label: 'Font Name and Size',
        buttons: [
            { type: 'select', label: 'Arial', value: 'fontname', disabled: true,
            menu: [
                { text: 'Arial', checked: true },
                { text: 'Arial Black' },
                { text: 'Comic Sans MS' },
                { text: 'Courier New' },
                { text: 'Lucida Console' },
                { text: 'Tahoma' },
                { text: 'Times New Roman' },
                { text: 'Trebuchet MS' },
                { text: 'Verdana' }
                ]
            },
            { type: 'spin', label: '13', value: 'fontsize', range: [ 9, 75 ], disabled: true }
            ]
        },
        { type: 'separator' },
        { group: 'textstyle', label: 'Font Style',
        buttons: [
            { type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold' },
            { type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic' },
            { type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline' },
            { type: 'separator' },
            { type: 'color', label: 'Font Color', value: 'forecolor', disabled: true },
            { type: 'color', label: 'Background Color', value: 'backcolor', disabled: true },
            { type: 'separator' },
            { type: 'push', label: 'Show/Hide Hidden Elements', value: 'hiddenelements' }
            ]
        },
        { type: 'separator' },
        { group: 'alignment', label: 'Alignment',
        buttons: [
            { type: 'push', label: 'Align Left CTRL + SHIFT + [', value: 'justifyleft' },
            { type: 'push', label: 'Align Center CTRL + SHIFT + |', value: 'justifycenter' },
            { type: 'push', label: 'Align Right CTRL + SHIFT + ]', value: 'justifyright' },
            { type: 'push', label: 'Justify', value: 'justifyfull' }
            ]
        },
        { type: 'separator' },
        { group: 'indentlist', label: 'Lists',
        buttons: [
            { type: 'push', label: 'Create an Unordered List', value: 'insertunorderedlist' },
            { type: 'push', label: 'Create an Ordered List', value: 'insertorderedlist' }
            ]
        },
        { type: 'separator' },
        { group: 'insertitem', label: 'Insert Item',
        buttons: [
            { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
            { type: 'push', label: 'Insert Image', value: 'insertimage' }
            ]
        }
        ]
};

function setupRichTextEditor()
{
    g_editor = new YAHOO.widget.Editor('body', {
                                       height: '300px',
                                       width: '750px',
                                       dompath: false, 
                                       animate: false,
                                       handleSubmit: false,
                                       toolbar: toolbar_config
                                       });
    
    g_editor.on('toolbarLoaded',onToolbarLoaded,g_editor,true);
    g_editor.render();
}

function onToolbarLoaded()
{
    var Dom = YAHOO.util.Dom;
    var Event = YAHOO.util.Event;

    var codeConfig = {
        type: 'push', 
        label: 'Edit HTML Code', 
        value: 'editcode'
    };
    this.toolbar.addButtonToGroup(codeConfig, 'insertitem');
    this.toolbar.on('editcodeClick', function() 
                    {
                        var ta = this.get('element'),
                        iframe = this.get('iframe').get('element');
                        
                        if (state == 'on') {
                            state = 'off';
                            this.toolbar.set('disabled', false);
                            this.setEditorHTML(ta.value);
                            if (!this.browser.ie) {
                                this._setDesignMode('on');
                            }
                        
                            Dom.removeClass(iframe, 'editor-hidden');
                            Dom.addClass(ta, 'editor-hidden');
                            this.show();
                            this._focusWindow();
                        } else {
                            state = 'on';
                            this.cleanHTML();
                            Dom.addClass(iframe, 'editor-hidden');
                            Dom.removeClass(ta, 'editor-hidden');
                            this.toolbar.set('disabled', true);
                            this.toolbar.getButtonByValue('editcode').set('disabled', false);
                            this.toolbar.selectButton('editcode');
                            //this.dompath.innerHTML = 'Editing HTML Code';
                            this.hide();
                        }
                    return false;
                    }, this, true);
    
    this.on('cleanHTML', function(ev) 
            {
                this.get('element').value = ev.html;
            }, this, true);
    
    this.on('afterRender', function() 
            {
                var wrapper = this.get('editor_wrapper');
                wrapper.appendChild(this.get('element'));
                this.setStyle('width', '100%');
                this.setStyle('height', '100%');
                this.setStyle('visibility', '');
                this.setStyle('top', '');
                this.setStyle('left', '');
                this.setStyle('position', '');
                
                this.addClass('editor-hidden');
            }, this, true);
}

