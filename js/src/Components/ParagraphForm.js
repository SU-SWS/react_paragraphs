import React from 'react';
import {Loader} from "./Atoms/Loader";
import {EntityForm} from "./EntityForm";

export const ParagraphForm = ({item, drupalContext, ...props}) => {

  if (typeof item.loadedEntity !== 'undefined') {
    drupalContext.loadEntity(item.id);
    return <Loader/>;
  }

  const onFieldChange = (fieldName, newValue) => {
    drupalContext.updateParagraph(item, fieldName, newValue);
  }

  return (
    <div className="item-form">
      <EntityForm
        entityType="paragraph"
        bundle={item.entity.type[0].target_id}
        entity={item.entity}
        onFieldChange={onFieldChange}
        drupalContext={drupalContext}
      />
    </div>
  )

};
