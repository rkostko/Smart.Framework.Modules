
-- START :: PostgreSQL Table: web / page_builder @ Sample Data r.180402 #####

INSERT INTO web.page_builder VALUES ('#my-segment-1', 'test-page', '', 0, 0, 0, 'Test Segment #1', 'html', 'UkVOREVSOg0KICBURVNULVNVQi1TRUdNRU5UOg0KICAgIGNvbnRlbnQ6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogc3ViLXNlZ21lbnQtYSAjIGh0bWwgc2VnbWVudA==', '<u><i><b>Das ist Segment 1</b></i></u>

<br>

<br>{{:TEST-SUB-SEGMENT:}}

<br>', '', '', '', '', '40246084b5bcb54981af8973cd5af1bd', 'admin', 1475172174, '2018-03-16 15:01:35');
INSERT INTO web.page_builder VALUES ('#sub-segment-a', '', '', 0, 0, 0, 'Test SUB-Segment A', 'html', 'I1JFTkRFUjoNCiMgIFRFU1QtQ0lSQ1VMQVI6DQojICAgIGNvbnRlbnQ6DQojICAgICAgdHlwZTogc2VnbWVudA0KIyAgICAgIGlkOiBteS1zZWdtZW50LTEgIyBodG1sIHNlZ21lbnQ=', '<font color="#ffffff"><span style="background-color: rgb(51, 102, 255);">Sub@Segment+A</span></font>

<br>

<br>{{:TEST-CIRCULAR:}}', '', '', '', '', 'c609e340b51b45cb8a9a6e929ab1e9cd', 'admin', 1475172564, '2018-03-16 15:01:48');
INSERT INTO web.page_builder VALUES ('pagina-unu', '', '', 0, 0, 0, 'Pagina Unu HTML', 'html', '', '', '', '', '', '', 'bef4ca0603c42a3f8f48e3af538483d7', 'admin', 1509729671, '2018-03-16 15:02:00');
INSERT INTO web.page_builder VALUES ('#my-segment-5', '', '', 0, 0, 0, 'Test Segment #5', 'settings', 'U0VUVElOR1M6DQogICAgYTogMjAwDQogICAgYjogJ3RoaXMgaXMn', '', '', '', '', '', 'bc28e11689a403eccc52d2cce100bf7e', 'admin', 1475171655, '2018-03-16 14:41:27');
INSERT INTO web.page_builder VALUES ('#my-segment-3', '', '', 0, 0, 0, 'Test Segment #3', 'markdown', 'Iw==', '# H1 (segment 3)

![Image 1](wpub/web-content/red-dragonfly.jpg "Image 1") {@width=200}', '', '', '', '', 'f9987b5a7f3fe6d69b6a53c707618143', 'admin', 1475171629, '2018-03-16 15:24:16');
INSERT INTO web.page_builder VALUES ('#my-segment-2', '', '', 0, 0, 0, 'Test Segment #2', 'html', '', '<font color="#ff0000"><b>This is segment 2</b></font>

<br>', '', '', '', '', '00947c35dff7c58e43f29add250c64b4', 'admin', 1475171599, '2018-03-16 15:00:54');
INSERT INTO web.page_builder VALUES ('#website-menu', '-', '', 0, 0, 1, '@ Website Menu @', 'html', '', '@This is the Menu@

<br>', '', '', '', '', 'f6f77b4ae67fe24b8ed4703e058f3efb', 'admin', 1475171957, '2018-03-16 15:01:23');
INSERT INTO web.page_builder VALUES ('test-page', '', '', 1, 0, 0, 'Test Page (HTML)', 'html', 'UkVOREVSOg0KICBURVNUOg0KICAgIGNvbnRlbnQ6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogbXktc2VnbWVudC0yICMgaHRtbCBzZWdtZW50DQogIEFSRUEtT05FOg0KICAgIGNvbnRlbnQ6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogbXktc2VnbWVudC0zICMgaHRtbCBzZWdtZW50DQogIEFSRUEuVFdPOg0KICAgIGNvbnRlbnQtMToNCiAgICAgIHR5cGU6IHBsdWdpbg0KICAgICAgaWQ6IHBhZ2UtYnVpbGRlci90ZXN0MQ0KICAgICAgY29uZmlnOg0KICAgICAgICB0aXRsZTogTXkgUGx1Z2luDQogICAgICAgIGNvbHVtbnM6IDEwMA0KIyAgICBjb250ZW50LTI6DQojICAgICAgdHlwZTogcGx1Z2luDQojICAgICAgaWQ6IGFub3VuY2VtZW50cy9tYWluDQogICAgY29udGVudC00Og0KICAgICAgdHlwZTogc2VnbWVudA0KICAgICAgaWQ6IG15LXNlZ21lbnQtMg0KICAgIGNvbnRlbnQtMzoNCiAgICAgIHR5cGU6IHNlZ21lbnQNCiAgICAgIGlkOiBteS1zZWdtZW50LTMgIyBtYXJrZG93biBzZWdtZW50DQogIEFSRUEtVEhSRUU6DQogICAgY29udGVudDoNCiAgICAgIHR5cGU6IHBsdWdpbg0KICAgICAgaWQ6IHBhZ2UtYnVpbGRlci90ZXN0Mg0KICAgICAgY29uZmlnOiBteS1zZWdtZW50LTUNCiAgQVJFQS1GT1VSOg0KICAgIGNvbnRlbnQ6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogbXktc2VnbWVudC0xDQogIEFSRUEtRklWRToNCiAgICBjb250ZW50LTE6DQogICAgICB0eXBlOiBwbHVnaW4NCiAgICAgIGlkOiBwYWdlLWJ1aWxkZXIvdGVzdDMNCiAgICAgIGNvbmZpZzoNCiAgICAgICAgdGl0bGU6IE5ld3MNCiAgICAgICAgY29sdW1uczogMTANCiAgICBjb250ZW50LTI6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogbXktc2VnbWVudC0yDQogICAgY29udGVudC0zOg0KICAgICAgdHlwZTogcGx1Z2luDQogICAgICBpZDogcGFnZS1idWlsZGVyL3Rlc3Q0DQogIFRFTVBMQVRFQEFSRUEuVE9QOg0KICAgIGNvbnRlbnQ6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogd2Vic2l0ZS1tZW51DQogIFRFTVBMQVRFQEFSRUEuRk9PVEVSOg0KICAgIGNvbnRlbnQ6DQogICAgICB0eXBlOiBzZWdtZW50DQogICAgICBpZDogd2Vic2l0ZS1mb290ZXI=', 'This is a test

<b>page</b>

!!

<br>

<br>{{:TEST:}}

<br>

<br>

<table style="width:100%;" cellpadding="2" cellspacing="2" border="1"><tbody>

<tr valign="top">

<td>{{:AREA-ONE:}}</td>

<td>{{:AREA.TWO:}}</td>

</tr>

</tbody></table>

<br>

{{:AREA-THREE:}}

<br>

<hr>
<table><tr><td>{{:AREA-FOUR:}}</td><td>{{:AREA-FIVE:}}</td></tr></table>
<hr>', 'This is a sample page <a>', 'The meta desc.', 'keywords, keyword', '', '1cd4c16b11903d307e4bf9707d4798f8', 'admin', 1475171339, '2018-04-02 08:55:15');
INSERT INTO web.page_builder VALUES ('#seg-plug', '', '', 0, 0, 0, 'Segment with Plugin', 'html', 'UkVOREVSOg0KICBQTFVHSU46DQogICAgY29udGVudDoNCiAgICAgIHR5cGU6IHBsdWdpbg0KICAgICAgaWQ6IHBhZ2UtYnVpbGRlci90ZXN0Mg0KICAgICAgY29uZmlnOiBteS1zZWdtZW50LTU=', '{{:PLUGIN:}}', '', '', '', '', '492c8a244802baa3273ef64f315db6fe', 'admin', 1522320028, '2018-04-02 08:21:24');
INSERT INTO web.page_builder VALUES ('raw-page', '', '', 1, 0, 0, 'Raw Page', 'raw', 'UFJPUFM6DQogIFJhd01pbWU6IHRleHQvcGxhaW4NCiAgUmF3RGlzcDogaW5saW5l', 'This is a raw page test', '', '', '', '', '5c66ef592eb6c3ba172b83b55c198ce9', 'admin', 1521213679, '2018-04-02 09:24:20');
INSERT INTO web.page_builder VALUES ('#website-footer', '-', '', 0, 0, 0, '@ Website Footer @', 'html', '', '<div style="background:#333333; color:#FFFFFF; width:100%; min-height:300px;">
  <h2>This is the footer area</h2>
</div>', '', '', '', '', '8929242ab0f6dbfd8484d9d6b2a352ce', 'admin', 1522167945, '2018-04-02 12:58:30');


--
-- PostgreSQL database dump complete
--

