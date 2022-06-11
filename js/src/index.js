import React from 'react';
import { createRoot } from 'react-dom/client';
import {Field} from './Components/Field';
import Modal from 'react-modal';

window.drupalSettings.reactParagraphs.forEach(item => {
  Modal.setAppElement('#' + item.fieldId);

  var paragraphsForm = document.getElementById(item.fieldId);

  const root = createRoot(paragraphsForm); // createRoot(container!) if you use TypeScript
  root.render(<Field {...item}/>);
});

