import React from 'react';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';

export const FormDialog = ({open, title, formContent, onClose, ...props}) => {
  let submitButton = React.createRef();

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

  return (
    <Dialog
      maxWidth='lg'
      open={open}
      aria-labelledby="max-width-dialog-title"
      onBackdropClick={onBackdropClick}
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
