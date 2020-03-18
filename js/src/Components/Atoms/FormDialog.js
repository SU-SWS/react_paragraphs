import React from 'react';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import Fade from '@material-ui/core/Fade';

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

  return (
    <Dialog
      maxWidth='lg'
      open={open}
      aria-labelledby="max-width-dialog-title"
      onBackdropClick={onBackdropClick}
      onEscapeKeyDown={onBackdropClick}
      style={{zIndex: 900}}
      onEntered={onEntered}
      TransitionProps={{ref: dialogRef}}
    >
      <DialogTitle style={{
        background: '#6b6b6b',
        color: '#fff',
        position: 'sticky',
        top: 0,
        zIndex: 1
      }}>
        {title}
      </DialogTitle>
      <form onSubmit={onFormSubmit}>
        <DialogContent>
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
