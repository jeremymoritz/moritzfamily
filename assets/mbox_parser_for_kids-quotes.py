import mailbox
import email
import csv

new_file_name = "clean_kids_mail.csv"

writer = csv.writer(open(new_file_name, "wb"))
for message in mailbox.mbox('KidsQuotes.mbox'):
  # b = email.message_from_string(a)
  # body = ""
  # if b.is_multipart():
  #     for payload in b.get_payload():
  #         # if payload.is_multipart(): ...
  #         print payload.get_payload()
  # else:
  #     print b.get_payload()

  # b = email.message_from_string(message)
  b = message
  body = ""
  if b.is_multipart():
    for part in b.walk():
      ctype = part.get_content_type()
      cdispo = str(part.get('Content-Disposition'))

      # skip any text/plain (txt) attachments
      if ctype == 'text/plain' and 'attachment' not in cdispo:
        body = part.get_payload(decode=True)  # decode
        break
  # not multipart - i.e. plain text, no attachments, keeping fingers crossed
  else:
    body = b.get_payload(decode=True)

  writer.writerow([
    message['subject'],
    message['from'],
    message['date'],
    body
  ])

print 'New file created: ' + new_file_name
