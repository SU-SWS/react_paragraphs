import React from 'react';
import ReactDOM from 'react-dom';
import {Field} from './Components/Field';
import Modal from 'react-modal';

window.drupalSettings.reactParagraphs.forEach(item => {
  Modal.setAppElement('#' + item.fieldId);

  var paragraphsForm = document.getElementById(item.fieldId);
  ReactDOM.render(<Field {...item}/>, paragraphsForm);
});

