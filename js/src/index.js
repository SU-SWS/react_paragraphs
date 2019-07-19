import React from 'react';
import ReactDOM from 'react-dom';
import {FieldWidget} from './Components/FieldWidget';
import Modal from 'react-modal';

window.drupalSettings.reactParagraphs.forEach(item => {
  Modal.setAppElement('#' + item.fieldId);

  var paragraphsForm = document.getElementById(item.fieldId);
  ReactDOM.render(<FieldWidget {...item}/>, paragraphsForm);
});

