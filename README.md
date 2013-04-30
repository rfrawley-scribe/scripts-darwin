Dawin Scripts (repo: scripts-darwin)
====================================

Set of simple script to help manage ScML, ePub and Mobi creation on Darwin systems (Mac OS X based).

## Install
A scripts directory should be created at the system root level on your computer. To clone directly from Git, I suggest the following:
* `mkdir ~/scripts` to create a scripts folder in your user home
* `git clone git://github.com/rfrawley-scribe/scripts-darwin.git ~/scripts` to clone this repository to the previously created folder
* `sudo mv ~/scripts /scripts` to move the folder to your system root

Once this has been done, it is likely you will want to be able to run the binaries/shell-scripts from your command prompt without performing a `cd` to the scripts directory.

This is easily accomplished by editing your Bash profile file, located at `~/.profile`, and adding the new scripts folder to your $PATH variable. Simply do:
* `nano -w ~/.profile` to open the text file in terminal; if the file exists with items already, perform the following at the bottom
* Add the line `export PATH="/scripts/_bin/:$PATH"`

All done! To save the document, type ^x (control and the "x" key), and follow the prompts. Now, if you open a terminal, the scripts will be available to you!

## Questions?!
Let me know! Contact Rob Frawley 2nd of Scribe Inc. at rfrawley@scribenet.com - thanks!