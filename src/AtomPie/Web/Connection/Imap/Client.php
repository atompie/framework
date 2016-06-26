<?php
namespace AtomPie\Web\Connection\Imap {

    use AtomPie\Web\Exception;
    use AtomPie\System\IO\File;

    class Client
    {
        private $sHost;
        private $iPort;
        private $sMode = 'imap';
        private $sSecurity;
        private $sLogin;
        private $sPassword;
        private $rStream;
        private $sTargetString;
        private $oStatus;

        public function __construct($sLogin, $sPassword, $sHost, $iPort = 25, $sSecurity = 'none')
        {
            $this->sHost = $sHost;
            $this->iPort = $iPort;
            $this->sSecurity = $sSecurity;
            $this->sLogin = $sLogin;
            $this->sPassword = $sPassword;
        }

        public function open($sMailbox = '', $bReadOnly = false)
        {
            if ($this->rStream) {
                $this->close();
            }
            $this->sTargetString = $this->getConnectionString($bReadOnly);
            $this->rStream = \imap_open($this->sTargetString . $sMailbox, $this->sLogin, $this->sPassword);
            if (!$this->rStream) {
                throw new Exception(implode(", ", imap_errors()));
            }
        }

        public function close()
        {
            if ($this->rStream) {
                imap_close($this->rStream);
            }
        }

        /**
         * @return object
         */
        public function check()
        {
            $this->oStatus = imap_check($this->rStream);
            return $this->oStatus;
        }

        public function hasNewMail()
        {
            $this->oStatus = $this->check();
            return $this->oStatus->Recent != 0;
        }

        public function deleteMessage($iMessageNo)
        {
            return imap_delete($this->rStream, $iMessageNo);
        }

        public function fetchAllMailOverview()
        {
            if (!isset($this->oStatus)) {
                $this->check();
            }
            if ($this->oStatus->Nmsgs <= 0) {
                return false;
            }
            return imap_fetch_overview($this->rStream, "1:{$this->oStatus->Nmsgs}", 0);
        }

        public function listMailboxes()
        {
            $mailboxes = imap_list($this->rStream, $this->sTargetString, "*");
            foreach ($mailboxes as $key=>$folder) {
                $mailboxes[$key] = str_replace($this->sTargetString, "", imap_utf7_decode($folder));
            }
            return $mailboxes;
        }

        public function countMessages()
        {
            return imap_num_msg($this->rStream);
        }

        function getMessageUid($iMessageNo)
        {
            return imap_uid($this->rStream, $iMessageNo);
        }

        public function getMessageNumber($uid)
        {
            return imap_msgno($this->rStream, $uid);
        }

        public function getLastMessageUid()
        {
            return $this->getMessageUid($this->countMessages());
        }

        public function getMessageHeader($uid)
        {
            return imap_header($this->rStream, imap_msgno($this->rStream, $uid));
        }

        public function getMessageHeaderFull($uid)
        {
            return imap_headerinfo($this->rStream, imap_msgno($this->rStream, $uid));
        }

        public function listMessages()
        {
            return imap_headers($this->rStream);
        }

        public function searchMessages($sSearch)
        {
            return imap_search($this->rStream, $sSearch);
        }

        public function getUnreadEmails()
        {
            $count = 0;
            $headers = imap_headers($this->rStream);
            foreach ($headers as $mail) {
                $flags = substr($mail, 0, 4);
                if (strpos($flags, "U") !== false) {
                    $count++;
                }
            }

            return $count;
        }

        public function status($mailbox)
        {
            return imap_status($this->rStream, $this->sTargetString . $mailbox, SA_ALL);
        }

        public function getBody($uid)
        {
            $body = $this->getPart($this->rStream, $uid, "TEXT/HTML");
            // if HTML body is empty, try getting text body
            if ($body == "") {
                $body = $this->getPart($uid, "TEXT/PLAIN");
            }
            return $body;
        }

        private function getPart($uid, $sMimeType, $bStructure = false, $partNumber = false)
        {
            if (!$bStructure) {
                $bStructure = imap_fetchstructure($this->rStream, $uid, FT_UID);
            }
            if (is_object($bStructure)) {
                if ($sMimeType == $this->getMimeType($bStructure)) {
                    if (!$partNumber) {
                        $partNumber = 1;
                    }
                    $text = imap_fetchbody($this->rStream, $uid, $partNumber, FT_UID);
                    return $this->decodeAttachment($text, $bStructure->encoding);
                }

                // Multi-part
                if ($bStructure->type == 1) {
                    foreach ($bStructure->parts as $index => $subStruct) {
                        $prefix = "";
                        if ($partNumber) {
                            $prefix = $partNumber . ".";
                        }
                        $data = $this->getPart($uid, $sMimeType, $subStruct, $prefix . ($index + 1));
                        if ($data) {
                            return $data;
                        }
                    }
                }
            }
            return false;
        }

        private function getMimeType($structure)
        {
            $primaryMimeType = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

            if ($structure->subtype) {
                return $primaryMimeType[(int)$structure->type] . "/" . $structure->subtype;
            }
            return "TEXT/PLAIN";
        }

        public function fetchStructure($uid)
        {
            return imap_fetchstructure($this->rStream, $uid, FT_UID);
        }

        /**
         * @param bool $bReadOnly
         * @return string
         */
        public function getConnectionString($bReadOnly = false)
        {
            if ($bReadOnly) {
                return "{" . $this->sHost . ":" . $this->iPort . "/" . $this->sMode . ($this->sSecurity != "none" ? "/" . $this->sSecurity . "/novalidate-cert" : "") . "/readonly}";
            }
            return "{" . $this->sHost . ":" . $this->iPort . "/" . $this->sMode . ($this->sSecurity != "none" ? "/" . $this->sSecurity . "/novalidate-cert" : "") . "}";
        }

        private function decodeAttachment($message, $encoding)
        {
            switch ($encoding) {
                case 0:
                case 1:
                    $message = imap_8bit($message);
                    break;
                case 2:
                    $message = imap_binary($message);
                    break;
                case 3:
                case 5:
                    $message = imap_base64($message);
                    break;
                case 4:
                    $message = imap_qprint($message);
                    break;
            }
            return $message;
        }

        public function saveAttachments($iMessageNumber, $sDirectory, $sSand = '')
        {

            $aAttachments = $this->getAttachments($iMessageNumber);

            foreach ($aAttachments as $sKey => $aAttachment) {
                if (isset($aAttachment['is_attachment']) && $aAttachment['is_attachment'] == true) {
                    if (isset($aAttachment['filename']) && !empty($aAttachment['filename'])) {
                        $sName = $aAttachment['filename'];
                    } else {
                        if (isset($aAttachment['name']) && !empty($aAttachment['name'])) {
                            $sName = $aAttachment['name'];
                        } else {
                            throw new Exception('Wrong attachment name.');
                        }
                    }

                    $oFile = new File($sDirectory . DIRECTORY_SEPARATOR . $sName);
                    $sFileName = $sDirectory . DIRECTORY_SEPARATOR . md5($sName . $sSand) . '.' . $oFile->getExtension();
                    $oFile->setName($sFileName);
                    $oFile->save($aAttachment['attachment']);


                    unset($aAttachments[$sKey]['attachment']);
                    $aAttachments[$sKey]['attachment'] = null;
                    unset($aAttachments[$sKey]['attachment']);
                    unset($aAttachments[$sKey]['is_attachment']);
                    $aAttachments[$sKey]['filename'] = $sFileName;
                    $aAttachments[$sKey]['name'] = $sName;
                } else {
                    unset($aAttachments[$sKey]);
                }
            }

            return $aAttachments;
        }

        private function getAttachments($iMessageNumber)
        {

            $rMailBox = $this->rStream;
            $oStructure = imap_fetchstructure($rMailBox, $iMessageNumber);

            $aAttachments = array();
            if (isset($oStructure->parts) && count($oStructure->parts)) {
                for ($i = 0; $i < count($oStructure->parts); $i++) {
                    $aAttachments[$i] = array(
                        'is_attachment' => false,
                        'filename' => '',
                        'name' => '',
                        'subtype' => '',
                        'attachment' => ''
                    );

                    if ($oStructure->parts[$i]->ifsubtype) {
                        $aAttachments[$i]['subtype'] = $oStructure->parts[$i]->subtype;
                    }
                    if ($oStructure->parts[$i]->ifdparameters) {
                        foreach ($oStructure->parts[$i]->dparameters as $object) {
                            if (strtolower($object->attribute) == 'filename') {
                                $aAttachments[$i]['is_attachment'] = true;
                                $aAttachments[$i]['filename'] = $this->decodeMimeString($object->value);
                            }
                        }
                    }

                    if ($oStructure->parts[$i]->ifparameters) {
                        foreach ($oStructure->parts[$i]->parameters as $object) {
                            if (strtolower($object->attribute) == 'name') {
                                $aAttachments[$i]['is_attachment'] = true;
                                $aAttachments[$i]['name'] = $this->decodeMimeString($object->value);
                            }
                        }
                    }

                    if ($aAttachments[$i]['is_attachment']) {
                        $aAttachments[$i]['attachment'] = $this->fetchBody($iMessageNumber, $i + 1,
                            $oStructure->parts[$i]->encoding);
                    }
                }
            }
            return $aAttachments;
        }

        public function decodeMimeString($sString)
        {
            return imap_utf8($sString);
        }

        private function fetchBody($iMessageIndex, $i, $sEncoding)
        {
            $sAttachment = imap_fetchbody($this->rStream, $iMessageIndex, $i);
            return $this->decodeAttachment($sAttachment, $sEncoding);
        }

        private static function parseHeader($sHeaders)
        {
            $sHeaders = preg_replace('/\r\n\s+/m', '', $sHeaders);
            preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $sHeaders, $aMatches);
            $aResult = array();
            foreach ($aMatches [1] as $sKey => $sValue) {
                $aResult[$sValue] = $aMatches[2][$sKey];
            }
            return $aResult;
        }

        public function isDeliveryFailed($oMessageOverview)
        {
            if ($this->isSubjectWithFailMessage($oMessageOverview)) {
                $aHeaders = self::parseHeader($this->fetchHeader($oMessageOverview->msgno));
                return $aHeaders;
            }
            return false;
        }

        public function fetchHeader($iMessageNumber)
        {
            return imap_fetchheader($this->rStream, $iMessageNumber, FT_PREFETCHTEXT);
        }

        /**
         * @param $oMessageOverview
         * @return bool
         */
        private function isSubjectWithFailMessage($oMessageOverview)
        {
            return isset($oMessageOverview->subject) && (
                strstr($oMessageOverview->subject, 'Mail delivery failed') ||
                strstr($oMessageOverview->subject, 'Delivery Status Notification (Failure)')
            );
        }


    }
}