import React from 'react';
import {FormDialog} from "./Atoms/FormDialog";
import {EntityForm} from "./EntityForm";
import {WidgetContext} from "../Contexts/WidgetManager";

export const RowForm = ({open, onClose, rowId, entity}) => {

  return (
    <WidgetContext.Consumer>
      {widgetContext =>
        <React.Fragment>
          <FormDialog
            open={open}
            onClose={onClose}
            title={'Edit Row'}
          >
            <EntityForm
              entityType="paragraphs_row"
              bundle={widgetContext.props.rowBundle}
              onFieldChange={(fieldName, newValue) => widgetContext.updateRowEntity(rowId, fieldName, newValue)}
              widgetContext={widgetContext}
              entity={entity}
            />
          </FormDialog>
        </React.Fragment>
      }
    </WidgetContext.Consumer>
  )

}
