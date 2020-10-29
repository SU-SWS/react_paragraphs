import React from 'react';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import {makeStyles} from "@material-ui/core/styles";

export const FormDialog = ({open, title, formContent, onClose, ...props}) => {
  let submitButton = React.createRef();
  let dialogRef = React.createRef();

  /**
   * Form submit handler.
   */
  const onFormSubmit = (e) => {
    e.preventDefault();
    onClose();
  };

  const onBackdropClick = () => {
    submitButton.current.click();
  };

  const onEntered = () => {
    // Remove the tab index because it breaks when there is a tab index and
    // the media browser is open.
    dialogRef.current.removeAttribute('tabindex');
  };

  const classes = useStyles();

  return (
    <Dialog
      maxWidth='md'
      fullWidth
      open={open}
      aria-labelledby="max-width-dialog-title"
      onBackdropClick={onBackdropClick}
      onEscapeKeyDown={onBackdropClick}
      style={{zIndex: 900}}
      onEntered={onEntered}
      TransitionProps={{ref: dialogRef}}
      classes={{ paper: classes.dialogPaper }}
    >
      <DialogTitle style={{
        background: '#6b6b6b',
        color: '#fff',
        position: 'sticky',
        top: 0,
        zIndex: 2
      }}>
        {title}
      </DialogTitle>
      <form onSubmit={onFormSubmit} style={{flex: '1 1 auto', display: 'flex', flexDirection: 'column'}}>
        <DialogContent classes={{root: classes.root}}>
          {props.children}
        </DialogContent>

        <DialogActions style={{
          position: 'sticky',
          bottom: 0,
          background: '#f5f5f2',
          padding: '15px'
        }}>
          <input
            ref={submitButton}
            className="button button--primary"
            type="submit"
            value="Continue"/>
        </DialogActions>

      </form>
    </Dialog>
  )
};

const useStyles = makeStyles(() => (
  {
    root:{
      padding: '0',
      '&:first-child': {padding: 0}
    },
    dialogPaper: {
      minHeight: '80vh',
      maxHeight: '80vh',
    },
  }
));
