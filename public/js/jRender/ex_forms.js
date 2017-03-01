// jRender Examples

/* FORMS */

var form = new jRender.Form.Form();

form.add({
   name: "username",
   type: "text",
   options: {
      label: "Username",
   },
   attributes: {
      placeholder: "user",
   }
}).add({
   name: "password",
   type: "password",
   options: {
      label: "password",
   },
   attributes: {
      placeholder: "pass",
   }
}).add({
   name: "submit",
   type: "submit",
   attributes: {
      value: "Login",
   }
});