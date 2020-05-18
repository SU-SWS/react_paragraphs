import React from 'react';
import {Loader} from "./Atoms/Loader";
import {EntityForm} from "./EntityForm";
import {FormDialog} from "./Atoms/FormDialog";
import {AdminTitleField} from "./Widgets/AdminTitleField";

export const ParagraphForm = ({item, widgetContext, ...props}) => {

  // if (typeof item.loadedEntity !== 'undefined') {
  //   widgetContext.loadEntity(item.id);
  //   return <Loader/>;
  // }

  return (
    <FormDialog
      title={`Edit ${props.typeLabel} > "${item.admin_title}"`}
      open={props.open}
      onClose={() => props.onClose(false)}
    >
      <AdminTitleField
        textField={props.open}
        item={item}
        onChange={widgetContext.onAdminTitleChange.bind(undefined, item.id)}
      />

      <div className="item-form">
        <EntityForm
          entityType="paragraph"
          bundle={item.entity.type[0].target_id}
          entity={item.entity}
          onFieldChange={(fieldName, newValue) => widgetContext.updateRowItemEntity(item, fieldName, newValue)}
          widgetContext={widgetContext}
        />
      </div>
    </FormDialog>
  )

};
