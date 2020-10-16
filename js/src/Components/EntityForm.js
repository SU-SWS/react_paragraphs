import React, {useState} from "react";
import { makeStyles } from '@material-ui/core/styles';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Box from '@material-ui/core/Box';
import {TextWidget} from "./Widgets/TextWidget";
import {LinkWidget} from "./Widgets/LinkWidget";
import {BooleanWidget} from "./Widgets/BooleanWidget";
import {DateWidget} from "./Widgets/DateWidget";
import {NumberWidget} from "./Widgets/NumberWidget";
import {SelectWidget} from "./Widgets/SelectWidget";
import {CheckboxesWidget} from "./Widgets/CheckboxesWidget";
import {RadiosWidget} from "./Widgets/RadiosWidget";
import {CkeditorWidget} from "./Widgets/CkeditorWidget";
import {MediaLibrary} from "./Widgets/MediaLibrary";
import {ViewFieldWidget} from "./Widgets/ViewFieldWidget";
import {AutocompleteReferenceWidget} from "./Widgets/AutocompleteReferenceWidget";
import {Loader} from "./Atoms/Loader";
import styled from "styled-components";
import {Behaviors} from "./Behaviors";

export const EntityForm = ({entityType, bundle, entity, onFieldChange, widgetContext}) => {

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
  const hasBehaviorFields = formFields.behavior_plugins.length !== 0;
  const [tabValue, setTabValue] = useState(hasFormFields ? 0 : 1);

  if (!hasFormFields && !hasBehaviorFields) {
    return <div>No fields to edit.</div>
  }

  const classes = useStyles();

  return (
    <div className={classes.root}>

      {(hasFormFields && hasBehaviorFields) &&
      <Tabs
        orientation="vertical"
        value={tabValue}
        onChange={(e, newValue) => setTabValue(newValue)}
        className={classes.tabs}
      >
        <Tab label="Content" className={classes.tab}/>
        <Tab label="Style"  className={classes.tab}/>
      </Tabs>
      }

      <TabPanel value={tabValue} index={0}>

        {Object.keys(formFields.form).map(fieldName => {
          const field = formFields.form[fieldName];
          const WidgetName = widgetComponents[field.widget_type];

          if (WidgetName === undefined) {
            console.error('Unable to find widget for type: ' + field.widget_type);
            return (
              <FieldContainer key={`widget-${fieldName}`}>
                Unable to provide a form for
                field {field.label}
              </FieldContainer>
            )
          }
          return (
            <FieldContainer key={`widget-${fieldName}`}>
              <WidgetName
                fieldId={fieldName}
                onFieldChange={onFieldChange.bind(undefined, fieldName)}
                settings={formFields.form[fieldName]}
                defaultValue={typeof entity[fieldName] !== 'undefined' ? entity[fieldName] : null}
                fieldName={fieldName}
              />
            </FieldContainer>
          )
        })}
      </TabPanel>


      {Object.keys(formFields.behavior_plugins).length >= 1 &&
      <TabPanel value={tabValue} index={1}>
        <Behaviors
          behaviors={formFields.behavior_plugins}
          widgetContext={widgetContext}
          entityType={entityType}
          bundle={bundle}
          entity={entity}
        />
      </TabPanel>
      }
    </div>
  )

}

const useStyles = makeStyles((theme) => (
  {
    root: {
      flexGrow: 1,
      backgroundColor: theme.palette.background.paper,
      display: 'flex',
      height: 224,
    },
    tabs: {
      borderRight: `1px solid ${theme.palette.divider}`,
      background: '#e6e5e1'
    },
    tab: {
      borderBottom: '1px solid #b3b2ad',
      background: '#f2f2f0'
    }
  }
));

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

const FieldContainer = styled.div`
  margin: 40px 0 0;
`;
