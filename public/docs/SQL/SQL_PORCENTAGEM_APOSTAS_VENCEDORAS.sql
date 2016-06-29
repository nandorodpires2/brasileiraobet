/* PORCENTAGEM APOSTAS VENCEDORAS */
select	case a.aposta_vencedora
				when 1 then 'Sim'
				when 0 then 'Não'
			end as vencedora,
			count(*) as apostas			
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
where		a.aposta_processada = 1			 
			and p.partida_realizada = 1
			and a.aposta_vencedora is not null
group by a.aposta_vencedora
