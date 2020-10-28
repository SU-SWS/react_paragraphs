import React from 'react';
import {Loader} from "./Atoms/Loader";
import {EntityForm} from "./EntityForm";
import {FormDialog} from "./Atoms/FormDialog";
import {AdminTitleField} from "./Widgets/AdminTitleField";

export const ParagraphForm = ({item, widgetContext, open, typeLabel, onClose}) => {

  if (open && typeof item.loadedEntity !== 'undefined') {
    widgetContext.loadRowItem(item.target_id);
    return <Loader/>;
  }

  return (
    <FormDialog
      title={`Edit ${typeLabel} > "${item.admin_title}"`}
      open={open}
      onClose={() => onClose(false)}
    >
      <AdminTitleField
        textField={open}
        item={item}
        onChange={widgetContext.onAdminTitleChange.bind(undefined, item.id)}
      />

      <div className="item-form">
        <EntityForm
          entityType="paragraph"
          bundle={item.entity.type[0].target_id}
          entity={item.entity}
          itemId={item.id}
          onFieldChange={(fieldName, newValue) => widgetContext.updateRowItemEntity(item, fieldName, newValue)}
          onBehaviorChange={widgetContext.updateEntityBehaviors.bind(undefined, item)}
          widgetContext={widgetContext}
        />
      </div>
    </FormDialog>
  )

};
