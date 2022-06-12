import React, {useState} from "react";
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import Box from '@mui/material/Box';
import {Loader} from "./Atoms/Loader";
import {Behaviors} from "./Behaviors";
import {TextWidget} from "./Widgets/TextWidget";
import {LinkWidget} from "./Widgets/LinkWidget";
import {BooleanWidget} from "./Widgets/BooleanWidget";
import {DateWidget} from "./Widgets/DateWidget";
import {NumberWidget} from "./Widgets/NumberWidget";
import {SelectWidget} from "./Widgets/SelectWidget";
import {CheckboxesWidget} from "./Widgets/CheckboxesWidget";
import {RadiosWidget} from "./Widgets/RadiosWidget";
import {CkeditorWidget} from "./Widgets/CkeditorWidget";
import {MediaLibrary} from "./Widgets/Media/MediaLibrary";
import {ViewFieldWidget} from "./Widgets/ViewFieldWidget";
import {AutocompleteReferenceWidget} from "./Widgets/AutocompleteReferenceWidget";

export const EntityForm = ({entityType, bundle, entity, itemId, onFieldChange, onBehaviorChange, widgetContext, ...props}) => {

  const widgetComponents = {
    text: TextWidget,
    link: LinkWidget,
    boolean: BooleanWidget,
    datetime: DateWidget,
    number: NumberWidget,
    select: SelectWidget,
    checkboxes: CheckboxesWidget,
    radios: RadiosWidget,
    ckeditor: CkeditorWidget,
    media_library: MediaLibrary,
    viewfield: ViewFieldWidget,
    entity_reference_autocomplete: AutocompleteReferenceWidget
  };

  const formFields = widgetContext.getEntityForm(entityType, bundle);

  if (typeof formFields === 'undefined') {
    return <Loader/>;
  }

  const hasFormFields = formFields.form.length !== 0
  const hasBehaviorFields = formFields.behavior_plugins.length !== 0
  const [tabValue, setTabValue] = useState(hasFormFields ? 0 : 1)

  if (!hasFormFields && !hasBehaviorFields) {
    return <div>No fields to edit.</div>
  }

  const tabPanelWidth = (hasFormFields && hasBehaviorFields) ? 'calc(100% - 170px)' : '100%';

  return (
    <div className="flex flex-grow">

      {(hasFormFields && hasBehaviorFields) &&
      <Tabs
        orientation="vertical"
        value={tabValue}
        onChange={(e, newValue) => setTabValue(newValue)}
        TabIndicatorProps={{ children: <span/>}}
        className="w-44 bg-stone-200 border-0 border-r border-gray-900/50 border-solid"
      >
        <Tab label="Content" sx={{borderBottom: '1px solid #b3b2ad', backgroundColor:'#f2f2f0'}}/>
        <Tab label="Style" sx={{borderBottom: '1px solid #b3b2ad', backgroundColor:'#f2f2f0'}}/>
      </Tabs>
      }

      <TabPanel value={tabValue} index={0} style={{width: tabPanelWidth}}>
        {props.header}
        {Object.keys(formFields.form).map(fieldName => {
          const field = formFields.form[fieldName];
          const WidgetName = widgetComponents[field.widget_type];

          if (WidgetName === undefined) {
            console.error('Unable to find widget for type: ' + field.widget_type);
            return (
              <div className="mt-20" key={`widget-${fieldName}`}>
                Unable to provide a form for
                field {field.label}
              </div>
            )

          }
          return (
            <div className="mt-20" key={`widget-${fieldName}`}>
              <WidgetName
                fieldId={fieldName}
                onFieldChange={onFieldChange.bind(undefined, fieldName)}
                settings={formFields.form[fieldName]}
                defaultValue={typeof entity[fieldName] !== 'undefined' ? entity[fieldName] : null}
                fieldName={fieldName}
                bundle={bundle}
              />
            </div>
          )
        })}
        {props.footer}
      </TabPanel>


      {Object.keys(formFields.behavior_plugins).length >= 1 &&
      <TabPanel value={tabValue} index={1} style={{width: tabPanelWidth}}>
        <Behaviors
          behaviors={formFields.behavior_plugins}
          onBehaviorChange={onBehaviorChange}
          entityType={entityType}
          bundle={bundle}
          entity={entity}
          itemId={itemId}
        />
      </TabPanel>
      }
    </div>
  )

}

const TabPanel = (props) => {
  const {children, value, index, ...other} = props;

  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`vertical-tabpanel-${index}`}
      aria-labelledby={`vertical-tab-${index}`}
      {...other}
    >
      {value === index && (
        <Box p={3}>
          {children}
        </Box>
      )}
    </div>
  );
}
