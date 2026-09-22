/**
 * This configuration was generated using the CKEditor 5 Builder. You can modify it anytime using this link:
 * https://builder.ckeditor.com/#installation/NoNgNARAzAdALDAjBScQHYQA4AMBWAJigE448tiQREt085FFioDEocsQc0duo4cBOKSjVMOFBADGAZxQ4wwRGAULlagLqQApgEMoURLoAmEDUA==
 */

import {
  ClassicEditor,
  Autosave,
  Essentials,
  Paragraph,
  Autoformat,
  TextTransformation,
  ListProperties,
  List,
  AdjacentListsSupport,
  TodoList,
  Bold,
  Italic,
  Underline,
  Strikethrough,
  Code,
  Subscript,
  Superscript,
  FontBackgroundColor,
  FontColor,
  FontFamily,
  FontSize,
  RemoveFormat,
  Highlight,
  GeneralHtmlSupport,
  Link,
} from "ckeditor5";

import translations from "ckeditor5/translations/cs.js";

const LICENSE_KEY =
  "eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3OTA5ODU1OTksImp0aSI6IjkzNDk3ZjNhLWM5MTItNDliZC04ZGE1LTI2MjFmOGVhM2MxYiIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiLCJzaCJdLCJ3aGl0ZUxhYmVsIjp0cnVlLCJsaWNlbnNlVHlwZSI6InRyaWFsIiwiZmVhdHVyZXMiOlsiKiJdLCJyZW1vdmVGZWF0dXJlcyI6WyJBSSJdLCJ2YyI6ImZiZWZjMzYxIn0.CMJx0oDZlQrTgODFntK_WPbp0IHwrCt0DnrPjRWi0dsSv2R5h9173RQYn-pMLXQX5UwAQ1Vbjro7e2ZgOfb5Lg";

const editorConfig = {
  attachTo: document.querySelector("#editor"),
  root: {
    placeholder: "Zde zadejte text aktuality...",
  },
  toolbar: {
    items: [
      "undo",
      "redo",
      "|",
      "bold",
      "italic",
      "underline",
      "strikethrough",
      "subscript",
      "superscript",
      "link",
      "removeFormat",
      "|",
      "bulletedList",
      "numberedList",
      "todoList",
      "|",
      "fontColor",
      "fontBackgroundColor",
    ],
    shouldNotGroupWhenFull: false,
  },
  plugins: [
    AdjacentListsSupport,
    Autoformat,
    Autosave,
    Bold,
    Essentials,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    GeneralHtmlSupport,
    Highlight,
    Italic,
    List,
    ListProperties,
    Paragraph,
    RemoveFormat,
    Strikethrough,
    Subscript,
    Superscript,
    TextTransformation,
    TodoList,
    Underline,
    Link,
  ],
  licenseKey: LICENSE_KEY,
  autosave: {
    /* See: https://ckeditor.com/docs/ckeditor5/latest/features/autosave.html */
  },
  fontFamily: {
    supportAllValues: true,
  },
  fontSize: {
    options: [10, 12, 14, "default", 18, 20, 22],
    supportAllValues: true,
  },
  htmlSupport: {
    allow: [
      {
        name: /^.*$/,
        styles: true,
        attributes: true,
        classes: true,
      },
    ],
  },
  language: "cs",
  list: {
    properties: {
      styles: true,
      startIndex: true,
      reversed: true,
    },
  },
  translations: [translations],
};

configUpdateAlert(editorConfig);

ClassicEditor.create(editorConfig)
  .then((editor) => {
    const form = document.querySelector("#article-form");

    form.addEventListener("submit", (event) => {
      const content = editor
        .getData()
        .replace(/<[^>]*>/g, "")
        .trim();

      if (!content) {
        event.preventDefault();
        alert("Obsah článku je povinný.");
      }
    });
  })
  .catch((error) => {
    console.error(error);
  });

/**
 * This function exists to remind you to update the config needed for premium features.
 * The function can be safely removed. Make sure to also remove call to this function when doing so.
 */
function configUpdateAlert(config) {
  if (configUpdateAlert.configUpdateAlertShown) {
    return;
  }

  const isModifiedByUser = (currentValue, forbiddenValue) => {
    if (currentValue === forbiddenValue) {
      return false;
    }

    if (currentValue === undefined) {
      return false;
    }

    return true;
  };

  const valuesToUpdate = [];

  configUpdateAlert.configUpdateAlertShown = true;

  if (!isModifiedByUser(config.licenseKey, "<YOUR_LICENSE_KEY>")) {
    valuesToUpdate.push("LICENSE_KEY");
  }

  if (valuesToUpdate.length) {
    window.alert(
      [
        "Please update the following values in your editor config",
        "to receive full access to Premium Features:",
        "",
        ...valuesToUpdate.map((value) => ` - ${value}`),
      ].join("\n"),
    );
  }
}
