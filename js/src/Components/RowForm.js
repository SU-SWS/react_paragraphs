import React from 'react';
import {FormDialog} from "./Atoms/FormDialog";
import {Loader} from "./Atoms/Loader";
import {EntityForm} from "./EntityForm";


export const RowForm = ({open, drupalContext, rowEntity, onClose}) => {
  const formFields = drupalContext.getEntityForm('paragraphs_row', 'basic_page_row');

  const onFieldChange = (fieldName, newValue) => {

  }

  if (formFields === undefined) {
    return <Loader/>
  }

  return (
    <FormDialog open={open} onClose={onClose}>
      <EntityForm
        entityType="paragraphs_row"
        bundle="basic_page_row"
        onFieldChange={onFieldChange}
        drupalContext={drupalContext}
        entity={{}}
      />
    </FormDialog>
  )

}
